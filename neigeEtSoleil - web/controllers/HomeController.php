<?php
require_once ("modele/modele.class.php");

class HomeController {
    private $unModele;

    public function __construct() {
        $this->unModele = new Modele();
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [
                'logement' => null,
                'activites' => [],
                'date_debut' => null,
                'date_fin' => null
            ];
        }
    }

    public function displayHome() {
        // 1. Récupération de toutes les habitations disponibles
        $toutes_habits = $this->unModele->selectAllHabitations();
        
        // 2. Préparation des données pour la section "À la une" (les 4 premières)
        $offres_une = array_slice($toutes_habits, 0, 4);

        // 3. Sélection DYNAMIQUE d'une région ayant au moins 4 habitations
        $nom_region_1      = "Nos coups de cœur";
        $logements_region_1 = [];

        $toutes_regions = $this->unModele->selectAllRegions();

        foreach ($toutes_regions as $region) {
            $logements_candidats = $this->unModele->selectHabitationsByRegion($region['ID_REG']);
            if (count($logements_candidats) >= 4) {
                $nom_region_1       = $region['NOM_REG'];
                $logements_region_1 = array_slice($logements_candidats, 0, 4);
                break;
            }
        }

        // 4. Sécurité : si aucune région n'a 4 logements, on affiche les 4 premières dispo
        if (empty($logements_region_1)) {
            $logements_region_1 = $offres_une;
        }

        // 5. On inclut la vue
        $unModele = $this->unModele;
        include("views/accueil.php");
    }

    // --- 1. MÉTHODE DETAILS ---
    public function details() {
        $habitation = null;
        $activites = [];
        
        // 1. Récupération des dates (Transmission conservée)
        $dates_preselectionnees = $_GET['dates_sejour'] ?? '';

        if (isset($_GET['id_habit'])) {
                $id_habit = (int)$_GET['id_habit'];
                $habitation = $this->unModele->selectHabitationById($id_habit);
                
                if ($habitation) {
                    $activites = $this->unModele->selectActivitesByHabit($id_habit);

                    // Dates indisponibles (réservations + hors-saison + périodes propriétaire)
                    $disableDates = $this->unModele->getDatesIndisponibles($id_habit);

                    $unModele = $this->unModele;
                    include("views/details_habitation.php");
                } else {
                    header("Location: index.php");
                    exit();
                }
            }
    }

    // --- 2. MÉTHODE DESTINATIONS ---
    public function destinations() {
        // 1. On récupère TOUTES les régions pour l'affichage des onglets/sections
        $regions = $this->unModele->selectAllRegions(); 
        
        // CORRECTION BUG 1 : Ajout de la récupération des stations
        $stations = $this->unModele->selectAllStations();

        // 2. On gère le filtre par station (bouton Explorer)
        $id_station = $_GET['id_station'] ?? null;

        if ($id_station) {
            // Si un ID est présent, on ne récupère que les habitations de cette station
            $habitations = $this->unModele->selectHabitationsByStation($id_station);
        } else {
            // Sinon on affiche tout par défaut
            $habitations = $this->unModele->selectAllHabitations();
        }

        // 3. Inclusion de la vue (Vérifie bien le nom du fichier !)
        $unModele = $this->unModele;
        include("views/destinations.php");
    }

    // --- 3. MÉTHODE SEARCH ---
    public function search() {
        // 1. Récupération des filtres
        $destination = $_GET['destination'] ?? '';
        $voyageurs   = $_GET['voyageurs'] ?? '';
        $dates       = $_GET['dates_sejour'] ?? ''; 

        // 2. On appelle la méthode du modèle qui gère le SQL (plus performant)
        // Cette méthode doit inclure WHERE STATUT_HABIT = 'disponible'
        $logements_trouves = $this->unModele->searchHabitations($destination, $voyageurs);

        // 3. Post-traitement pour la vue (si votre vue attend des clés spécifiques)
        // On harmonise les index pour correspondre à resultats_recherche.php
        foreach ($logements_trouves as &$h) {
            $h['VILLE'] = $h['VILLE'] ?? $h['VILLE_HABIT'] ?? 'Ville inconnue';
            $h['IMAGE'] = $h['IMAGE'] ?? $h['IMAGE_HABIT'] ?? 'default.jpg';
            $h['NB_CHAMBRES'] = $h['NB_CHAMBRES'] ?? $h['NB_CHAMBRES_HABIT'] ?? 0;
        }

        // 4. Inclusion de la vue
        $unModele = $this->unModele;
        include("views/resultats_recherche.php");
    }

    // --- 4. GESTION DES RÉSERVATIONS (LOGEMENT) ---
    public function addToCart() {
        // Validation : vérifier que les dates sont présentes
        if (!isset($_POST['dates_sejour']) || empty($_POST['dates_sejour'])) {
            $_SESSION['flash_error'] = "Veuillez sélectionner vos dates de séjour avant de réserver.";
            header("Location: index.php?action=details&id_habit=" . ($_POST['id_habit'] ?? ''));
            exit();
        }
        
        // Initialiser le panier avec la nouvelle structure si nécessaire
        if (!isset($_SESSION['panier']['reservations'])) {
            $_SESSION['panier']['reservations'] = [];
        }
        
        $dates_brutes = $_POST['dates_sejour'];
        error_log("[DEBUG addToCart] dates_sejour brut = " . var_export($dates_brutes, true));
        // Extraction robuste de 2 dates JJ/MM/AAAA, peu importe le séparateur exact
        // (gère espaces normales, insécables \u00A0, "au", "-", etc.)
        preg_match_all('/(\d{2}\/\d{2}\/\d{4})/', $dates_brutes, $matches);
        $tab_dates = $matches[1];
        error_log("[DEBUG addToCart] dates extraites = " . var_export($tab_dates, true));
        
        if (count($tab_dates) == 2) {
            // Conversion du format FR (dd/mm/yyyy) vers SQL (yyyy-mm-dd)
            $debutObj = DateTime::createFromFormat('d/m/Y', trim($tab_dates[0]));
            $finObj   = DateTime::createFromFormat('d/m/Y', trim($tab_dates[1]));

            if (!$debutObj || !$finObj) {
                $_SESSION['flash_error'] = "Format de dates invalide. Veuillez sélectionner vos dates via le calendrier.";
                header("Location: index.php?action=details&id_habit=" . ($_POST['id_habit'] ?? ''));
                exit();
            }

            $date_debut = $debutObj->format('Y-m-d');
            $date_fin = $finObj->format('Y-m-d');
            
            // NOUVELLE LOGIQUE : Vérifier les chevauchements avec les autres réservations du panier
            foreach ($_SESSION['panier']['reservations'] as $resa) {
                if (isset($resa['date_debut']) && isset($resa['date_fin'])) {
                    // Vérifier si les dates se chevauchent
                    if ($date_debut < $resa['date_fin'] && $date_fin > $resa['date_debut']) {
                        $_SESSION['flash_error'] = "Ces dates chevauchent une autre réservation dans votre panier (du " . 
                            date('d/m/Y', strtotime($resa['date_debut'])) . " au " . 
                            date('d/m/Y', strtotime($resa['date_fin'])) . "). Veuillez choisir d'autres dates.";
                        header("Location: index.php?action=details&id_habit=" . ($_POST['id_habit'] ?? ''));
                        exit();
                    }
                }
            }
            
            // Récupérer le logement
            if (isset($_POST['id_habit'])) {
                $id_habit = $_POST['id_habit'];
                $logement = $this->unModele->selectHabitationById($id_habit);
                
                if ($logement) {
                    // Ajouter une nouvelle réservation au panier
                    $_SESSION['panier']['reservations'][] = [
                        'logement' => $logement,
                        'date_debut' => $date_debut,
                        'date_fin' => $date_fin,
                        'activites' => []
                    ];
                    
                    $_SESSION['flash_success'] = "Logement ajouté à vos réservations !";
                } else {
                    $_SESSION['flash_error'] = "Erreur : logement introuvable.";
                }
            }
        } else {
            $_SESSION['flash_error'] = "Veuillez sélectionner une plage de dates complète (du ... au ...).";
            header("Location: index.php?action=details&id_habit=" . ($_POST['id_habit'] ?? ''));
            exit();
        }
        
        header("Location: index.php?action=panier");
        exit();
    }

    // --- 5. MÉTHODE ADD ACTIVITY ---
    public function addActivityToCart() {
        if (isset($_POST['id_act']) && isset($_POST['id_habit'])) {
            $id_act = $_POST['id_act'];
            $id_habit = $_POST['id_habit'];
            
            // Initialiser si nécessaire
            if (!isset($_SESSION['panier']['reservations'])) {
                $_SESSION['panier']['reservations'] = [];
            }
            
            // Trouver la réservation correspondante à ce logement
            $reservation_trouvee = false;
            foreach ($_SESSION['panier']['reservations'] as $index => &$resa) {
                if ($resa['logement']['ID_HABIT'] == $id_habit) {
                    // Ajouter l'activité à cette réservation
                    $resa['activites'][$id_act] = [
                        'id' => $id_act,
                        'nom' => $_POST['nom_act'] ?? 'Activité',
                        'prix' => $_POST['prix_act'] ?? 0,
                        'date' => $_POST['date_act'] ?? ''
                    ];
                    $reservation_trouvee = true;
                    break;
                }
            }
            
            if ($reservation_trouvee) {
                $_SESSION['flash_success'] = "Activité ajoutée à votre réservation !";
            } else {
                $_SESSION['flash_error'] = "Veuillez d'abord ajouter le logement à vos réservations.";
            }
            
            header("Location: index.php?action=details&id_habit=" . $id_habit);
            exit();
        } else {
            $_SESSION['flash_error'] = "Erreur : impossible d'ajouter l'activité.";
            header("Location: index.php?action=accueil");
            exit();
        }
    }

    public function supprimerActivite() {
        if (isset($_GET['index_resa']) && isset($_GET['id_act'])) {
            $index_resa = $_GET['index_resa'];
            $id_act = $_GET['id_act'];
            
            if (isset($_SESSION['panier']['reservations'][$index_resa]['activites'][$id_act])) {
                unset($_SESSION['panier']['reservations'][$index_resa]['activites'][$id_act]);
                $_SESSION['flash_success'] = "Activité supprimée de votre réservation.";
            }
        }
        header("Location: index.php?action=panier");
        exit();
    }

    public function supprimerReservation() {
        if (isset($_GET['index'])) {
            $index = $_GET['index'];
            
            if (isset($_SESSION['panier']['reservations'][$index])) {
                unset($_SESSION['panier']['reservations'][$index]);
                // Réindexer le tableau
                $_SESSION['panier']['reservations'] = array_values($_SESSION['panier']['reservations']);
                $_SESSION['flash_success'] = "Réservation supprimée de votre panier.";
            }
        }
        header("Location: index.php?action=panier");
        exit();
    }

    public function viderLogement() {
        // Cette méthode n'est plus utilisée avec la nouvelle structure
        header("Location: index.php?action=panier");
        exit();
    }

    public function confirmerToutesReservations() {
        if (!isset($_SESSION['id_user'])) {
            $_SESSION['flash_error'] = "Vous devez être connecté pour confirmer vos réservations.";
            header("Location: index.php?action=connexion");
            exit();
        }

        if (!isset($_SESSION['panier']['reservations']) || empty($_SESSION['panier']['reservations'])) {
            $_SESSION['flash_error'] = "Votre panier est vide.";
            header("Location: index.php?action=panier");
            exit();
        }

        $reservations_confirmees = 0;
        $erreurs = [];

        foreach ($_SESSION['panier']['reservations'] as $index => $resa) {
            $id_habit = $resa['logement']['ID_HABIT'];
            $date_debut = $resa['date_debut'];
            $date_fin = $resa['date_fin'];
            
            // Vérifier les chevauchements en BDD
            $chevauchement = $this->unModele->verifierChevauchementDates($id_habit, $date_debut, $date_fin);
            
            if ($chevauchement) {
                $erreurs[] = "Réservation #" . ($index + 1) . " : dates déjà réservées";
                continue;
            }

            // Calcul du prix
            $debut = new DateTime($date_debut);
            $fin = new DateTime($date_fin);
            $nb_nuits = $debut->diff($fin)->days;
            $prix_logement = $nb_nuits * $resa['logement']['PRIX_NUIT_HABIT'];
            
            $prix_activites = 0;
            if (!empty($resa['activites'])) {
                foreach ($resa['activites'] as $act) {
                    $prix_activites += $act['prix'];
                }
            }
            
            $prix_total = $prix_logement + $prix_activites;

            // Préparation des données
            $donnees = [
                'id_user' => $_SESSION['id_user'],
                'id_habit' => $id_habit,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'nb_personnes' => $resa['logement']['NB_GUETS_HABIT'],
                'prix_total' => $prix_total
            ];

            // Insertion de la réservation
            $id_reservation = $this->unModele->insertReservation($donnees);
            
            // Insertion des activités
            if ($id_reservation > 0 && !empty($resa['activites'])) {
                foreach ($resa['activites'] as $act) {
                    $this->unModele->insertReservationActivite($id_reservation, $act);
                }
            }

            if ($id_reservation > 0) {
                $reservations_confirmees++;

                // Envoi de l'email de confirmation
                $user = $this->unModele->selectUserById($_SESSION['id_user']);
                $this->envoyerEmailConfirmation($user, $resa, $id_reservation, $prix_total);
            }
        }

        // Nettoyage du panier
        unset($_SESSION['panier']);
        
        // Messages de retour
        if ($reservations_confirmees > 0) {
            $_SESSION['flash_success'] = $reservations_confirmees . " réservation(s) confirmée(s) avec succès ! Un email de confirmation vous a été envoyé.";
        }
        
        if (!empty($erreurs)) {
            $_SESSION['flash_error'] = implode(". ", $erreurs);
        }
        
        header("Location: index.php?action=profile");
        exit();
    }

    // --- ENVOI EMAIL DE CONFIRMATION ---
    private function envoyerEmailConfirmation($user, $resa, $id_reservation, $prix_total) {
        // Adresse email de destination — modifiez selon vos besoins
        $email_client = $user['EMAIL_USER'] ?? '';
        if (empty($email_client)) return;

        $nom_complet   = ($user['PRENOM_USER'] ?? '') . ' ' . ($user['NOM_USER'] ?? '');
        $titre_logement = $resa['logement']['TITRE_HABIT'] ?? 'Logement';
        $ville          = $resa['logement']['VILLE_HABIT'] ?? '';
        $date_debut     = date('d/m/Y', strtotime($resa['date_debut']));
        $date_fin       = date('d/m/Y', strtotime($resa['date_fin']));
        $debut_obj      = new DateTime($resa['date_debut']);
        $fin_obj        = new DateTime($resa['date_fin']);
        $nb_nuits       = $debut_obj->diff($fin_obj)->days;

        $sujet = "Confirmation de votre réservation #$id_reservation — Neige et Soleil";

        $corps  = "Bonjour $nom_complet,

";
        $corps .= "Votre réservation a bien été confirmée. Voici le récapitulatif :

";
        $corps .= "  Logement    : $titre_logement ($ville)
";
        $corps .= "  Arrivée     : $date_debut
";
        $corps .= "  Départ      : $date_fin
";
        $corps .= "  Durée       : $nb_nuits nuit(s)
";
        $corps .= "  Prix total  : " . number_format($prix_total, 2) . " €
";

        if (!empty($resa['activites'])) {
            $corps .= "
Activités incluses :
";
            foreach ($resa['activites'] as $act) {
                $corps .= "  - " . ($act['nom'] ?? 'Activité') . " (" . number_format($act['prix'], 2) . " €)
";
            }
        }

        $corps .= "
Merci pour votre confiance.
";
        $corps .= "L'équipe Neige et Soleil
";
        $corps .= "contact@neigeetsoleil.fr
";

        $entetes  = "From: Neige et Soleil <contact@neigeetsoleil.fr>
";
        $entetes .= "Reply-To: contact@neigeetsoleil.fr
";
        $entetes .= "Content-Type: text/plain; charset=UTF-8
";
        $entetes .= "X-Mailer: PHP/" . phpversion();

        @mail($email_client, $sujet, $corps, $entetes);
    }

    // --- RÉSERVATIONS SE TERMINANT AUJOURD'HUI (pour invitation à évaluer) ---
    public function getReservationsAEvaluerAujourdhui($id_user) {
        return $this->unModele->selectReservationsFinAujourdhui($id_user);
    }

    // --- PAGE À PROPOS ---
    public function apropos() {
        include("views/apropos.php");
    }
}