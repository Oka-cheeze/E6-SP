<?php
class Modele {
    protected $pdo;

    public function __construct() {
        $url = "mysql:host=localhost;dbname=neige_et_soleilppe;charset=utf8";
        $user = "root";
        $mdp = "";
        try {
            $this->pdo = new PDO($url, $user, $mdp);
        } catch (PDOException $exp) {
            echo "Erreur de connexion à la bdd : " . $exp->getMessage();
        }
    }

    /****************** GESTION DES HABITATIONS (CORRIGÉ) ******************/

    // Fonction privée pour insérer la base commune dans HABITATION
    private function insertHabitationBase($tab) {
        $requete = "INSERT INTO HABITATION (
                        ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, 
                        SURFACE_HABIT, NB_CHAMBRES_HABIT, ADRESSE_HABIT, 
                        CP_HABIT, VILLE_HABIT, IMAGE_HABIT, 
                        NB_GUETS_HABIT, NB_LITS_HABIT, PRIX_NUIT_HABIT, STATUT_HABIT
                    ) VALUES (
                        :id_pro, :id_station, :id_reg, :titre, 
                        :surface, :nb_c, :adresse, 
                        :cp, :ville, :image, 
                        :guests, :beds, :prix, 'en_attente'
                    );";

        $prep = $this->pdo->prepare($requete);
        
        // On crée un tableau propre avec UNIQUEMENT les clés de la requête
        $data = array(
            ':id_pro'     => $tab['id_pro'],
            ':id_station' => $tab['id_station'],
            ':id_reg'     => 1, // On force à 1 si non présent dans $tab
            ':titre'      => $tab['titre'],
            ':surface'    => $tab['surface'],
            ':nb_c'       => $tab['nb_c'],
            ':adresse'    => $tab['adresse'],
            ':cp'         => $tab['cp'],
            ':ville'      => $tab['ville'],
            ':image'      => $tab['image'],
            ':guests'     => $tab['guests'],
            ':beds'       => $tab['beds'],
            ':prix'       => $tab['prix']
        );

        $prep->execute($data);
        return $this->pdo->lastInsertId();
    }

    private function uploadImage($files) {
        if (isset($files['image']) && $files['image']['error'] == 0) {
            // Création d'un nom unique pour éviter les doublons
            $nom = time() . "_" . basename($files['image']['name']);
            $destination = "assets/img/locations/" . $nom;
            
            // Déplacement du fichier du dossier temporaire vers ton dossier final
            if (move_uploaded_file($files['image']['tmp_name'], $destination)) {
                return $destination; // Retourne le chemin à enregistrer en BDD
            }
        }
        return null; // Si pas d'image, on retourne null
    }

    public function insertHabitation($tab, $files) {
        // 1. On traite l'image d'abord
        $tab['image'] = $this->uploadImage($files); 
        
        // 2. On appelle la fonction privée pour l'insertion SQL
        return $this->insertHabitationBase($tab);
    }

        public function getDatesIndisponibles($id_habit) {
        $requete = "SELECT DATE_DEBUT_RES, DATE_FIN_RES FROM RESERVATION 
                    WHERE ID_HABIT = :id AND STATUT_RES = 'confirmee'";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([':id' => $id_habit]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectHabitationsEnAttente() {
        // On ajoute une jointure sur la table STATION pour avoir le nom de la ville
        $requete = "SELECT H.*, S.NOM_STATION 
                    FROM HABITATION H
                    LEFT JOIN STATION S ON H.ID_STATION = S.ID_STATION
                    WHERE H.STATUT_HABIT = 'en_attente';";
        $res = $this->pdo->query($requete);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validerHabitation($id_habit) {
        // 1. Mettre à jour le statut de l'habitation
        $req1 = "UPDATE HABITATION SET STATUT_HABIT = 'disponible' WHERE ID_HABIT = :id";
        $this->pdo->prepare($req1)->execute([':id' => $id_habit]);

        // 2. Mettre à jour le contrat (déclenche aussi le trigger trg_update_statut_apres_contrat)
        $req2 = "UPDATE CONTRAT SET STATUT_CONTRAT = 'valide'
                  WHERE ID_HABIT = :id AND STATUT_CONTRAT = 'en_attente'
                  ORDER BY ID_CONTRAT DESC LIMIT 1";
        $this->pdo->prepare($req2)->execute([':id' => $id_habit]);
    }

    public function refuserHabitationAvecMotif($id_habit, $motif) {
        // Met à jour le dernier contrat en_attente — le trigger trg_update_statut_apres_contrat
        // se charge automatiquement de passer STATUT_HABIT à 'rejete' dans HABITATION
        $req = "UPDATE CONTRAT SET STATUT_CONTRAT = 'refuse', MOTIF_REFUS_CONTRAT = :motif
                WHERE ID_HABIT = :id AND STATUT_CONTRAT = 'en_attente'
                ORDER BY ID_CONTRAT DESC LIMIT 1";
        $this->pdo->prepare($req)->execute([':motif' => $motif, ':id' => $id_habit]);
    }

    public function selectContratByHabit($id_habit) {
        $requete = "SELECT * FROM CONTRAT WHERE ID_HABIT = ?";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([$id_habit]);
        return $prep->fetch(PDO::FETCH_ASSOC);
    }

    public function selectDernierContratByHabit($id_habit) {
        // On trie par ID_CONTRAT descendant pour avoir le dernier archivé
        $requete = "SELECT * FROM CONTRAT WHERE ID_HABIT = ? ORDER BY ID_CONTRAT DESC LIMIT 1";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([$id_habit]);
        return $prep->fetch(PDO::FETCH_ASSOC);
    }

    public function selectHabitationsByProprio($id_pro) {
        $requete = "SELECT * FROM HABITATION WHERE ID_PRO = :id_pro";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([':id_pro' => $id_pro]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAllHabitations() {
        $requete = "SELECT ID_HABIT, ID_PRO, ID_STATION, ID_REG, TITRE_HABIT,
                        DESCRIPTION_HABIT, ADRESSE_HABIT, VILLE_HABIT, CP_HABIT,
                        PRIX_NUIT_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
                        NB_GUETS_HABIT, NB_LITS_HABIT, IMAGE_HABIT, STATUT_HABIT
                    FROM HABITATION
                    WHERE STATUT_HABIT = 'disponible'";
        try {
            $res = $this->pdo->query($requete);
            return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            error_log('[selectAllHabitations] ' . $e->getMessage());
            return [];
        }
    }

    public function selectHabitationsByRegion($id_region) {
        // Récupère toutes les habitations disponibles d'une région donnée
        $requete = "SELECT ID_HABIT, ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, 
                        ADRESSE_HABIT, VILLE_HABIT, CP_HABIT, PRIX_NUIT_HABIT, 
                        SURFACE_HABIT, NB_CHAMBRES_HABIT, 
                        NB_GUETS_HABIT, NB_LITS_HABIT, IMAGE_HABIT, STATUT_HABIT 
                    FROM HABITATION 
                    WHERE STATUT_HABIT = 'disponible' 
                    AND ID_REG = :id_region;";

        try {
            $prep = $this->pdo->prepare($requete);
            $prep->execute([':id_region' => $id_region]);
            return $prep->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function searchHabitations($destination = "", $voyageurs = "") {
        // 1. On utilise des JOIN pour accéder aux noms des stations et régions
        $sql = "SELECT h.ID_HABIT, h.TITRE_HABIT, h.VILLE_HABIT, 
                    h.IMAGE_HABIT, h.NB_CHAMBRES_HABIT, 
                    h.PRIX_NUIT_HABIT 
                FROM HABITATION h
                LEFT JOIN STATION s ON h.ID_STATION = s.ID_STATION
                LEFT JOIN REGION r ON h.ID_REG = r.ID_REG
                WHERE h.STATUT_HABIT = 'disponible'"; // Filtre de base indispensable

        $params = [];

        // 2. Recherche multicritères sur la destination
        if (!empty($destination)) {
            $sql .= " AND (h.VILLE_HABIT LIKE :dest 
                        OR h.CP_HABIT LIKE :dest 
                        OR s.NOM_STATION LIKE :dest 
                        OR r.NOM_REG LIKE :dest)";
            $params[':dest'] = '%' . $destination . '%';
        }
        
        // 3. Filtre sur le nombre de voyageurs (attention à l'orthographe NB_GUETS_HABIT de votre SQL)
        if (!empty($voyageurs)) {
            $sql .= " AND h.NB_GUETS_HABIT >= :guests";
            $params[':guests'] = $voyageurs;
        }

        try {
            $prepare = $this->pdo->prepare($sql);
            $prepare->execute($params);
            return $prepare->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPhotosByHabit($id_habit) {
        $requete = "SELECT ID_PHOTO, ID_HABIT, CHEMIN_PHOTO FROM PHOTO WHERE ID_HABIT = ?";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([$id_habit]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateHabitation($tab, $files = []) {
        // CORRECTION BUG 7 : Mapping correct des champs
        $requete = "UPDATE HABITATION SET 
                    TITRE_HABIT = '" . ($tab['titre'] ?? '') . "', 
                    VILLE_HABIT = '" . ($tab['ville'] ?? '') . "', 
                    ADRESSE_HABIT = '" . ($tab['adresse'] ?? '') . "', 
                    CP_HABIT = '" . ($tab['cp'] ?? '') . "', 
                    SURFACE_HABIT = " . ($tab['surface'] ?? 0) . ", 
                    NB_CHAMBRES_HABIT = " . ($tab['nb_c'] ?? 0) . ", 
                    NB_LITS_HABIT = " . ($tab['beds'] ?? 0) . ", 
                    NB_GUETS_HABIT = " . ($tab['guests'] ?? 0) . ", 
                    PRIX_NUIT_HABIT = " . ($tab['prix'] ?? 0) . "
                    WHERE ID_HABIT = " . ($tab['id_habit'] ?? 0);

        $this->pdo->exec($requete);

        // Gestion de l'upload d'image si un nouveau fichier est fourni
        if (isset($files['image_habitation']) && $files['image_habitation']['error'] == 0) {
            $extension = strtolower(pathinfo($files['image_habitation']['name'], PATHINFO_EXTENSION));
            $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($extension, $extensionsAutorisees)) {
                $nomImage = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
                $destination = "assets/img/locations/" . $nomImage;
                
                if (move_uploaded_file($files['image_habitation']['tmp_name'], $destination)) {
                    $reqImage = "UPDATE HABITATION SET IMAGE_HABIT = '" . $destination . "' WHERE ID_HABIT = " . $tab['id_habit'];
                    $this->pdo->exec($reqImage);
                }
            }
        }

        // Mise à jour des équipements : on supprime les anciens et on remet les nouveaux
        $this->pdo->exec("DELETE FROM POSSEDER WHERE ID_HABIT = " . $tab['id_habit']);
        if (isset($tab['equipements'])) {
            foreach ($tab['equipements'] as $id_equip) {
                $this->pdo->exec("INSERT INTO POSSEDER (ID_HABIT, ID_EQUIP) VALUES (" . $tab['id_habit'] . ", " . $id_equip . ")");
            }
        }
    }

    public function getEquipementsByHabit($id_habit) {
        $requete = "SELECT E.NOM_EQUIP FROM EQUIPEMENT E 
                    JOIN POSSEDER P ON E.ID_EQUIP = P.ID_EQUIP 
                    WHERE P.ID_HABIT = ?";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([$id_habit]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectEquipementsByHabitation($id_habit) {
        $requete = "SELECT E.NOM_EQUIP, E.ICONE_EQUIP 
                    FROM EQUIPEMENT E, POSSEDER P 
                    WHERE E.ID_EQUIP = P.ID_EQUIP 
                    AND P.ID_HABIT = " . $id_habit;
        return $this->pdo->query($requete)->fetchAll();
    }

    /****************** RECHERCHE ET DÉTAILS ******************/

    public function selectHabitationById($id_habit) {
        // On récupère une habitation précise par son ID
        $requete = "SELECT * FROM HABITATION WHERE ID_HABIT = " . $id_habit;
        try {
            $res = $this->pdo->query($requete);
            return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    

    public function selectHabitationsByStation($id_station) {
        $requete = "SELECT * FROM HABITATION 
                    WHERE ID_STATION = :id_station 
                    AND STATUT_HABIT = 'disponible'";
        try {
            $select = $this->pdo->prepare($requete);
            $select->execute([':id_station' => $id_station]);
            return $select->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function selectAllRegions() {
        $requete = "SELECT * FROM REGION";
        try {
            $res = $this->pdo->query($requete);
            return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function selectAllStations() {
        $requete = "SELECT * FROM STATION";
        try {
            $res = $this->pdo->query($requete);
            return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function selectAllActivites() {
        $requete = "SELECT * FROM ACTIVITE";
        try {
            $res = $this->pdo->query($requete);
            return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function insertAppartement($tab) {
        $id_habit = $this->insertHabitationBase($tab);
        // On utilise ETAGE_APP et ASCENSEUR_APP
        $requete = "INSERT INTO APPARTEMENT (ID_HABIT, ETAGE_APP, ASCENSEUR_APP) 
                    VALUES (:id_habit, :etage, :ascenseur)";
        
        $prep = $this->pdo->prepare($requete);
        $prep->execute(array(
            ":id_habit"  => $id_habit,
            ":etage"     => $tab['etage'] ?? 0,
            ":ascenseur" => $tab['ascenseur'] ?? 0
        ));
        return $id_habit;
    }

    public function insertMaison($tab) {
        $id_habit = $this->insertHabitationBase($tab);
        // CORRECTION BUG 3 : On utilise NB_ETAGES_MAI et JARDIN_MAI avec les bons champs
        $requete = "INSERT INTO MAISON (ID_HABIT, NB_ETAGES_MAI, JARDIN_MAI) 
                    VALUES (:id_habit, :nb_etages, :jardin)";

        $prep = $this->pdo->prepare($requete);
        $prep->execute(array(
            ":id_habit"   => $id_habit,
            ":nb_etages"  => $tab['nb_etages_maison'] ?? 1, // Correction : utilisation du bon champ
            ":jardin"     => $tab['jardin'] ?? 0
        ));
        return $id_habit;
    }

    public function insertChalet($tab) {
        $id_habit = $this->insertHabitationBase($tab);
        // CORRECTION BUG 3 : On utilise TYPE_BOIS_CHAL et CHEMINEE_CHA
        $requete = "INSERT INTO CHALET (ID_HABIT, TYPE_BOIS_CHAL, CHEMINEE_CHA) 
                    VALUES (:id_habit, :type_bois, :cheminee)";

        $prep = $this->pdo->prepare($requete);
        
        // Si la checkbox "bois" est cochée, on met "Bois extérieur", sinon "Standard"
        $type_bois = isset($tab['bois']) && $tab['bois'] == 1 ? 'Bois extérieur' : 'Standard';
        
        $prep->execute(array(
            ":id_habit"  => $id_habit,
            ":type_bois" => $type_bois,
            ":cheminee"  => $tab['cheminee'] ?? 0
        ));
        return $id_habit;
    }

    /****************** GESTION UTILISATEURS ******************/

    public function insertUser($tab) {
        $qUser = "INSERT INTO USER (NOM_USER, PRENOM_USER, EMAIL_USER, MDP_USER, ROLE_USER, ADRESSE_USER, VILLE_USER, CP_USER) 
                VALUES (
                    '" . $tab['nom'] . "', 
                    '" . $tab['prenom'] . "', 
                    '" . $tab['email'] . "', 
                    '" . $tab['mdp'] . "', 
                    '" . $tab['role'] . "',
                    '" . $tab['adresse'] . "',
                    '" . $tab['ville'] . "',
                    '" . $tab['cp'] . "'
                );";
        
        $this->pdo->exec($qUser);
        $id_user = $this->pdo->lastInsertId();

        if ($tab['role'] == 'client') {
            $qFille = "INSERT INTO CLIENT (ID_USER, DATE_NAISSANCE_CLI) 
                    VALUES (" . $id_user . ", '" . $tab['date_n'] . "');";
            $this->pdo->exec($qFille);
        } else if ($tab['role'] == 'proprietaire') {
            $qFille = "INSERT INTO PROPRIETAIRE (ID_USER) VALUES (" . $id_user . ");";
            $this->pdo->exec($qFille);
        }
    }

// 1. POUR L'ADMIN (La nouvelle)
    public function selectUserById($id_user) {
        $requete = "SELECT * FROM USER WHERE ID_USER = :id_user;";
        $prep = $this->pdo->prepare($requete);
        $prep->execute(array(":id_user" => $id_user));
        return $prep->fetch(PDO::FETCH_ASSOC);
    }

    // 2. POUR LE CLIENT (Celle qui manque à la ligne 23 de UserController)
    public function selectClientById($id_user) {
        $requete = "SELECT * FROM CLIENT C, USER U 
                    WHERE C.ID_USER = U.ID_USER 
                    AND U.ID_USER = :id_user;";
        $prep = $this->pdo->prepare($requete);
        $prep->execute(array(":id_user" => $id_user));
        return $prep->fetch(PDO::FETCH_ASSOC);
    }

    // 3. POUR LE PROPRIÉTAIRE
    public function selectProprietaireById($id_user) {
        $requete = "SELECT * FROM PROPRIETAIRE P, USER U 
                    WHERE P.ID_USER = U.ID_USER 
                    AND U.ID_USER = :id_user;";
        $prep = $this->pdo->prepare($requete);
        $prep->execute(array(":id_user" => $id_user));
        return $prep->fetch(PDO::FETCH_ASSOC);
    }


    /****************** GESTION DES EQUIPEMENTS (CORRIGÉ) ******************/

    public function selectAllEquipements() {
        return $this->pdo->query("SELECT ID_EQUIP, NOM_EQUIP FROM EQUIPEMENT")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function renseignementEquipement($id_habit, $id_equip) {
        $requete = "INSERT INTO POSSEDER (ID_HABIT, ID_EQUIP) VALUES (?, ?)";
        $this->pdo->prepare($requete)->execute([$id_habit, $id_equip]);
    }

    public function deleteHabitation($id_habit) {
        // 1. On supprime d'abord les liaisons équipements
        $this->pdo->exec("DELETE FROM POSSEDER WHERE ID_HABIT = " . $id_habit);
        
        // 2. On supprime la spécialisation (Appart/Maison/Chalet)
        $this->pdo->exec("DELETE FROM APPARTEMENT WHERE ID_HABIT = " . $id_habit);
        $this->pdo->exec("DELETE FROM MAISON WHERE ID_HABIT = " . $id_habit);
        $this->pdo->exec("DELETE FROM CHALET WHERE ID_HABIT = " . $id_habit);
        
        // 3. Enfin, on supprime l'habitation elle-même
        $requete = "DELETE FROM HABITATION WHERE ID_HABIT = " . $id_habit;
        return $this->pdo->exec($requete);
    }

    /****************** GESTION DES ACTIVITES (FUSIONNÉE) ******************/

    public function insertActivite($tab) {
        // On utilise les colonnes exactes de votre SQL : NOM_ACT, DESCRIPTION_ACT, PRIX_ACT, TYPE_ACT
        $requete = "INSERT INTO ACTIVITE (NOM_ACT, DESCRIPTION_ACT, PRIX_ACT, TYPE_ACT) 
                    VALUES (:nom, :desc, :prix, :type);";
        
        $prepare = $this->pdo->prepare($requete);
        $prepare->execute(array(
            ":nom"  => $tab['nom_activite'], 
            ":desc" => $tab['description_act'],
            ":prix" => $tab['tarif_activite'],
            ":type" => $tab['type_activite'] ?? 'Sport' 
        ));
    }

    public function deleteActivite($id_act) {
        $requete = "DELETE FROM ACTIVITE WHERE ID_ACT = ?";
        $this->pdo->prepare($requete)->execute([$id_act]);
    }

    // Cette méthode est cruciale pour votre vue "vue_select_habitation.php"
    public function selectActivitesByHabit($id_habit) {
        $requete = "SELECT A.* FROM ACTIVITE A
                    JOIN PROPOSER P ON A.ID_ACT = P.ID_ACT
                    JOIN STATION S ON P.ID_STATION = S.ID_STATION
                    JOIN HABITATION H ON S.ID_STATION = H.ID_STATION
                    WHERE H.ID_HABIT = :id_h";
                    
        $prepare = $this->pdo->prepare($requete);
        $prepare->execute([":id_h" => $id_habit]);
        return $prepare->fetchAll(PDO::FETCH_ASSOC);
    }

        /****************** GESTION DES UTILISATEURS / CLIENTS / PROPRIETAIRES ******************/

    public function verifConnexion($email, $mdp) {
        $requete = "SELECT * FROM USER 
                    WHERE EMAIL_USER = '" . $email . "' 
                    AND MDP_USER = '" . $mdp . "';";
        $res = $this->pdo->query($requete);
        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function inscriptionUser($data) {
        // Calcul de l'âge à partir de date_n
        $age = 0;
        if (!empty($data['date_n'])) {
            $age = (new DateTime())->diff(new DateTime($data['date_n']))->y;
        }

        try {
            // ÉTAPE 1 : INSERT dans USER (table mère commune)
            $qUser = "INSERT INTO USER
                          (NOM_USER, PRENOM_USER, AGE_USER, TEL_USER, ADRESSE_USER, CP_USER, VILLE_USER, EMAIL_USER, MDP_USER, ROLE_USER)
                      VALUES
                          (:nom, :prenom, :age, :tel, :adresse, :cp, :ville, :email, :mdp, :role)";
            $prep = $this->pdo->prepare($qUser);
            $prep->execute([
                ':nom'     => trim($data['nom']     ?? ''),
                ':prenom'  => trim($data['prenom']  ?? ''),
                ':age'     => $age,
                ':tel'     => trim($data['tel']     ?? ''),
                ':adresse' => trim($data['adresse'] ?? ''),
                ':cp'      => trim($data['cp']      ?? ''),
                ':ville'   => trim($data['ville']   ?? ''),
                ':email'   => trim($data['email']   ?? ''),
                ':mdp'     => trim($data['mdp']     ?? ''),
                ':role'    => $data['role'] === 'proprietaire' ? 'proprietaire' : 'client',
            ]);

            $id_user = $this->pdo->lastInsertId();
            if (!$id_user) return false;

            // ÉTAPE 2 : INSERT dans la table spécifique au rôle
            if ($data['role'] === 'proprietaire') {
                $qRole = "INSERT INTO PROPRIETAIRE (ID_USER, RIB_PRO) VALUES (:id, :rib)";
                $prep2 = $this->pdo->prepare($qRole);
                $prep2->execute([
                    ':id'  => $id_user,
                    ':rib' => trim($data['rib'] ?? ''),
                ]);
            } else {
                $qRole = "INSERT INTO CLIENT (ID_USER, DATE_NAISSANCE_CLI) VALUES (:id, :date_n)";
                $prep2 = $this->pdo->prepare($qRole);
                $prep2->execute([
                    ':id'     => $id_user,
                    ':date_n' => !empty($data['date_n']) ? $data['date_n'] : null,
                ]);
            }

            return true;

        } catch (PDOException $e) {
            error_log("[inscriptionUser] Erreur : " . $e->getMessage());
            return false;
        }
    }

    public function verifierExistenceCompte($email, $tel, $nom, $prenom) {
        // Requête large pour trouver n'importe quelle correspondance
        $requete = "SELECT U.EMAIL_USER, C.TEL_CLI, P.TEL_PRO, U.NOM_USER, U.PRENOM_USER 
                    FROM USER U
                    LEFT JOIN CLIENT C ON U.EMAIL_USER = C.EMAIL_CLI
                    LEFT JOIN PROPRIETAIRE P ON U.EMAIL_USER = P.EMAIL_PRO
                    WHERE U.EMAIL_USER = '$email' 
                    OR C.TEL_CLI = '$tel' 
                    OR P.TEL_PRO = '$tel'
                    OR (U.NOM_USER = '$nom' AND U.PRENOM_USER = '$prenom');";
                    
        return $this->pdo->query($requete)->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($tab) {
        // 1. Mise à jour de la table mère USER
        $qUser = "UPDATE USER SET NOM_USER = :nom, PRENOM_USER = :prenom, ADRESSE_USER = :adr 
                WHERE ID_USER = :id";
        $prep = $this->pdo->prepare($qUser);
        $prep->execute(array(
            ":nom"    => $tab['nom'],
            ":prenom" => $tab['prenom'],
            ":adr"    => $tab['adresse'],
            ":id"     => $tab['id_user']
        ));

        // 2. Mise à jour de la table fille selon le rôle
        if ($tab['role'] == 'client') {
            $qFille = "UPDATE CLIENT SET DATE_NAISSANCE_CLI = :date_n WHERE ID_USER = :id";
            $prepFille = $this->pdo->prepare($qFille);
            $prepFille->execute(array(":date_n" => $tab['date_n'], ":id" => $tab['id_user']));
        }
    }

    public function insertReservation($tab) {
        $requete = "INSERT INTO RESERVATION
                        (ID_USER, ID_HABIT, DATE_RES, DATE_DEBUT_RES,
                         DATE_FIN_RES, NB_PERSONNES, PRIX_TOTAL_RES, STATUT_RES)
                    VALUES
                        (:id_user, :id_habit, NOW(), :date_debut,
                         :date_fin, :nb_personnes, :prix_total, 'confirmee')";
        try {
            $prep = $this->pdo->prepare($requete);
            $prep->execute([
                ':id_user'      => (int)$tab['id_user'],
                ':id_habit'     => (int)$tab['id_habit'],
                ':date_debut'   => $tab['date_debut'],
                ':date_fin'     => $tab['date_fin'],
                ':nb_personnes' => (int)$tab['nb_personnes'],
                ':prix_total'   => (float)$tab['prix_total'],
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('[insertReservation] ' . $e->getMessage());
            return 0;
        }
    }

    public function selectReservationsByClient($id_user) {
        // Joint HABITATION et AVIS pour avoir le titre, le prix et savoir si un avis existe
        $requete = "SELECT R.*, H.TITRE_HABIT, H.VILLE_HABIT, H.IMAGE_HABIT,
                        A.NOTE_AVIS, A.COMMENTAIRE_AVIS, A.ID_AVIS
                    FROM RESERVATION R
                    JOIN HABITATION H ON R.ID_HABIT = H.ID_HABIT
                    LEFT JOIN AVIS A ON R.ID_RES = A.ID_RES
                    WHERE R.ID_USER = :id_user
                    ORDER BY R.DATE_DEBUT_RES DESC";
                    
        try {
            $prep = $this->pdo->prepare($requete);
            $prep->execute([':id_user' => $id_user]);
            return $prep->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('[selectReservationsByClient] ' . $e->getMessage());
            return [];
        }
    }

    // CORRECTION BUG 4 : Nouvelle méthode pour insérer les activités dans une réservation
    public function insertReservationActivite($id_reservation, $activite) {
        $requete = "INSERT INTO RESERVATION_ACTIVITE (ID_RES, ID_ACT, DATE_ACTIVITE, PRIX_ACT) 
                    VALUES (
                        " . $id_reservation . ", 
                        " . $activite['id'] . ", 
                        '" . $activite['date'] . "', 
                        " . $activite['prix'] . "
                    );";
        
        try {
            $this->pdo->exec($requete);
            return true;
        } catch (PDOException $e) {
            // En cas d'erreur (table inexistante par exemple), on ne bloque pas tout
            return false;
        }
    }

    // CORRECTION BUG 4 : Récupérer les activités d'une réservation
    public function selectActivitesByReservation($id_reservation) {
        $requete = "SELECT A.*, RA.DATE_ACTIVITE, RA.PRIX_ACT as PRIX_RESERVATION
                    FROM RESERVATION_ACTIVITE RA
                    JOIN ACTIVITE A ON RA.ID_ACT = A.ID_ACT
                    WHERE RA.ID_RES = " . $id_reservation;
        
        try {
            $res = $this->pdo->query($requete);
            return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    // NOUVELLE MÉTHODE : Vérifier si des dates se chevauchent pour un logement
    public function verifierChevauchementParClient($id_user, $date_debut, $date_fin, $id_reservation_exclue = null) {
        // Vérifie si ce CLIENT a déjà une réservation qui chevauche les dates demandées
        // (peu importe le logement — un client ne peut pas être dans deux endroits en même temps)
        $requete = "SELECT COUNT(*) as nb
                    FROM RESERVATION
                    WHERE ID_USER = :id_user
                    AND STATUT_RES IN ('confirmee', 'en_attente')
                    AND DATE_DEBUT_RES < :date_fin
                    AND DATE_FIN_RES  > :date_debut";

        // Permet d'exclure la réservation en cours de modification si besoin
        if ($id_reservation_exclue) {
            $requete .= " AND ID_RES != :id_res";
        }

        try {
            $prep = $this->pdo->prepare($requete);
            $params = [
                ':id_user'    => $id_user,
                ':date_debut' => $date_debut,
                ':date_fin'   => $date_fin,
            ];
            if ($id_reservation_exclue) {
                $params[':id_res'] = $id_reservation_exclue;
            }
            $prep->execute($params);
            $result = $prep->fetch(PDO::FETCH_ASSOC);
            return $result['nb'] > 0;
        } catch (PDOException $e) {
            error_log('[verifierChevauchementParClient] ' . $e->getMessage());
            return false;
        }
    }

    public function verifierChevauchementDates($id_habit, $date_debut, $date_fin) {
        $requete = "SELECT COUNT(*) as nb_chevauchements
                    FROM RESERVATION
                    WHERE ID_HABIT = :id_habit
                    AND STATUT_RES IN ('confirmee', 'en_attente')
                    AND (
                        (DATE_DEBUT_RES <= :date_fin AND DATE_FIN_RES >= :date_debut)
                    )";
        
        try {
            $prep = $this->pdo->prepare($requete);
            $prep->execute([
                ':id_habit' => $id_habit,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin
            ]);
            
            $result = $prep->fetch(PDO::FETCH_ASSOC);
            return $result['nb_chevauchements'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

        /** --- GESTION DES INDISPONIBILITÉS (Calendrier rouge) --- **/


    /** --- GESTION DES AVIS (Lien avec Réservation) --- **/
    // Réservations dont le dernier jour est aujourd'hui et sans avis déposé
    public function selectReservationsFinAujourdhui($id_user) {
        $requete = "SELECT R.*, H.TITRE_HABIT, H.VILLE_HABIT, H.IMAGE_HABIT
                    FROM RESERVATION R
                    JOIN HABITATION H ON R.ID_HABIT = H.ID_HABIT
                    LEFT JOIN AVIS A ON R.ID_RES = A.ID_RES
                    WHERE R.ID_USER = :id_user
                    AND R.STATUT_RES = 'confirmee'
                    AND R.DATE_FIN_RES = CURDATE()
                    AND A.ID_AVIS IS NULL";
        try {
            $prep = $this->pdo->prepare($requete);
            $prep->execute([':id_user' => $id_user]);
            return $prep->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Vérifier si un avis existe déjà pour une réservation
    public function selectAvisByRes($id_res) {
        $requete = "SELECT * FROM AVIS WHERE ID_RES = :id_res LIMIT 1";
        $prep = $this->pdo->prepare($requete);
        $prep->execute([':id_res' => $id_res]);
        return $prep->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAvis($id_res, $note, $commentaire) {
        $requete = "INSERT INTO AVIS (ID_RES, NOTE_AVIS, COMMENTAIRE_AVIS, DATE_AVIS) 
                    VALUES (?, ?, ?, CURDATE())";
        $prep = $this->pdo->prepare($requete);
        return $prep->execute([$id_res, $note, $commentaire]);
    }

    public function getAvisParHabitation($id_habit, $tous = false) {
        $sql = "SELECT A.*, U.PRENOM_USER FROM AVIS A
                JOIN RESERVATION R ON A.ID_RES = R.ID_RES
                JOIN USER U ON R.ID_USER = U.ID_USER
                WHERE R.ID_HABIT = ?";
        if (!$tous) { $sql .= " AND A.DATE_AVIS >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)"; }
        $sql .= " ORDER BY A.DATE_AVIS DESC";
        $prep = $this->pdo->prepare($sql);
        $prep->execute([$id_habit]);
        return $prep->fetchAll(PDO::FETCH_ASSOC);
    }

}