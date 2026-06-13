<?php
require_once ("modele/modele.class.php");

class OwnerController {
    private $unModele;

    public function __construct() {
        // Initialisation du modèle pour accéder à la base de données
        $this->unModele = new Modele();
    }

    public function displayDashboard() {
        $id_pro = $_SESSION['id_user'] ?? null;
            if (!$id_pro) { header("Location: index.php?action=connexion"); exit(); }

            $mes_annonces = $this->unModele->selectHabitationsByProprio($id_pro);
            
            $actifsUniquement = 0;
            foreach ($mes_annonces as $annonce) {
                $statut = $annonce['STATUT_HABIT'] ?? ''; 
                if ($statut === 'disponible') {
                    $actifsUniquement++;
                }
            }

        $stats = [
            'revenus' => "0", 
            'reservations' => 0,
            'note' => "5.0",
            'actifs' => $actifsUniquement // On utilise la variable filtrée
        ];
        
        // CORRECTION BUG 2 : Ajout de la récupération des équipements pour la modale
        $lesEquipements = $this->unModele->selectAllEquipements();
        $unModele = $this->unModele;
        include("views/dashboard_owner.php");
    }

    
public function ajouterLogement() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. On récupère l'ID du propriétaire connecté
        $id_pro = $_SESSION['id_user'] ?? null;
        
        // 2. Préparation des données
        $donnees = $_POST;
        $donnees['id_pro'] = $id_pro;

        // 3. Appel de la méthode PUBLIQUE du modèle
        // Cette méthode va gérer l'image puis appeler insertHabitationBase
        $id_genere = $this->unModele->insertHabitation($donnees, $_FILES);

        if ($id_genere > 0) {
            // 4. Gestion des équipements dans la table POSSEDER
            if (isset($_POST['equipements']) && is_array($_POST['equipements'])) {
                foreach ($_POST['equipements'] as $id_equip) {
                    $this->unModele->renseignementEquipement($id_genere, $id_equip);
                }
            }
            
            header("Location: index.php?action=dashboard&msg=ajoute");
            exit();
        } else {
            header("Location: index.php?action=dashboard&msg=erreur");
            exit();
        }
    }
}

    /**
     * Cette méthode remplace "ajouterAuParc".
     * Elle gère l'upload de l'image et l'insertion en BDD via le modèle.
     */
    public function traiterAjoutLogement($post, $files) {
        // 1. Remapping précis : Formulaire HTML -> Variables PHP
        // On utilise les 'name' exacts de vue_gestion_habitation.php
        $tab = array();
        $tab['id_pro']     = $_SESSION['id_user'] ?? 1;
        $tab['id_station'] = $post['id_station'] ?? 1; 
        $tab['id_reg']     = $post['id_reg'] ?? 1; // Ajouté car requis par ton SQL
        $tab['titre']      = $post['titre'];
        $tab['type']       = $post['type_bien']; 
        $tab['adresse']    = $post['adresse'] ?? '';
        $tab['ville']      = $post['ville'] ?? '';
        $tab['cp']         = $post['cp'] ?? '00000';
        $tab['prix']       = $post['prix'];    // Était 'prix_nuit' dans ton erreur, c'est 'prix' en HTML
        $tab['surface']    = $post['surface'];
        $tab['nb_c']       = $post['nb_c'];    // Était 'nb_chambres' dans ton erreur, c'est 'nb_c' en HTML
        $tab['guests']     = $post['guests'];  // Était 'nb_voyageurs' dans ton erreur, c'est 'guests' en HTML
        $tab['beds']       = $post['beds'];    // Était 'nb_lits' dans ton erreur, c'est 'beds' en HTML
        $tab['description']= $post['description'] ?? '';

        // CORRECTION BUG 3 : Ajout des champs spécifiques par type
        // Pour Appartement
        $tab['etage'] = isset($post['etage']) ? (int)$post['etage'] : 0;
        $tab['ascenseur'] = isset($post['ascenseur']) ? 1 : 0; // Checkbox = 1 si cochée, 0 sinon
        
        // Pour Maison
        $tab['nb_etages_maison'] = isset($post['nb_etages_maison']) ? (int)$post['nb_etages_maison'] : 1;
        $tab['jardin'] = isset($post['jardin']) ? 1 : 0;
        
        // Pour Chalet
        $tab['bois'] = isset($post['bois']) ? 1 : 0;
        $tab['cheminee'] = isset($post['cheminee']) ? 1 : 0;

        // 2. Gestion de l'image (Upload)
        $imagePath = "assets/img/locations/default.jpg";
        if (isset($files['image_habitation']) && $files['image_habitation']['error'] == 0) {
            $extension = strtolower(pathinfo($files['image_habitation']['name'], PATHINFO_EXTENSION));
            $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($extension, $extensionsAutorisees)) {
                $nomImage = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
                $destination = "assets/img/locations/" . $nomImage;
                
                if (!is_dir("assets/img/locations/")) {
                    mkdir("assets/img/locations/", 0777, true);
                }

                if (move_uploaded_file($files['image_habitation']['tmp_name'], $destination)) {
                    $imagePath = $destination;
                }
            }
        }
        $tab['image'] = $imagePath;

        // 3. Insertion via le modèle
        // On appelle une méthode du modèle qui va gérer l'INSERT dans HABITATION + la table spécifique
        $id_genere = 0; // Initialisation de la variable qui va recevoir l'ID

        switch($tab['type']) {
            case 'appart': 
                // Cette fonction appelle insertHabitationBase() et retourne l'ID
                $id_genere = $this->unModele->insertAppartement($tab); 
                break;
                
            case 'maison': 
                $id_genere = $this->unModele->insertMaison($tab); 
                break;
                
            case 'chalet': 
                $id_genere = $this->unModele->insertChalet($tab); 
                break;
        }

        // 4. Liaison des équipements
        if ($id_genere > 0 && isset($post['equipements'])) {
            foreach ($post['equipements'] as $id_equip) {
                $this->unModele->renseignementEquipement($id_genere, $id_equip);
            }
        }

        // 5. Enregistrement des périodes d'indisponibilité
        if ($id_genere > 0 && !empty($post['periode_debut']) && is_array($post['periode_debut'])) {
            foreach ($post['periode_debut'] as $i => $debut) {
                $fin = $post['periode_fin'][$i] ?? null;
                if (!empty($debut) && !empty($fin)) {
                    $this->unModele->insertPeriodeDisponibilite($id_genere, $debut, $fin);
                }
            }
        }

        if ($id_genere > 0) {
            // On redirige vers le dashboard avec le paramètre de message
            header("Location: index.php?action=dashboard&msg=ajoute");
            exit(); // Toujours mettre exit après une redirection
        } else {
            // En cas d'erreur d'insertion, on peut renvoyer au dashboard sans message ou avec un message d'erreur
            header("Location: index.php?action=dashboard&msg=erreur");
            exit();
        }
        
        return $id_genere;
    }

    public function supprimerLogement() {
        if (isset($_POST['id_suppr'])) {
            $id_habit = $_POST['id_suppr'];
            $this->unModele->deleteHabitation($id_habit);
            // On utilise 'supprime' pour correspondre au test dans la vue
            header("Location: index.php?action=dashboard&msg=supprime");
            exit();
        }
    }

    public function modifierLogement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ajout de la gestion des fichiers si ton modèle le permet
            $this->unModele->updateHabitation($_POST, $_FILES); 

            // Mise à jour des périodes d'indisponibilité
            if (isset($_POST['id_habit'])) {
                $this->unModele->deletePeriodesDisponibilite($_POST['id_habit']);
                if (!empty($_POST['periode_debut']) && is_array($_POST['periode_debut'])) {
                    foreach ($_POST['periode_debut'] as $i => $debut) {
                        $fin = $_POST['periode_fin'][$i] ?? null;
                        if (!empty($debut) && !empty($fin)) {
                            $this->unModele->insertPeriodeDisponibilite($_POST['id_habit'], $debut, $fin);
                        }
                    }
                }
            }

            // On ajoute le paramètre 'modifie'
            header("Location: index.php?action=dashboard&msg=modifie");
            exit();
        }
    }
}