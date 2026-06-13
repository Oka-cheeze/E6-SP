<?php
session_start();

// 1. Inclusions des contrôleurs
require_once 'modele/modele.class.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/OwnerController.php';
require_once 'controllers/AdminController.php';

// 2. Instanciation des contrôleurs de base (toujours accessibles)
$homeController = new HomeController();
$userController = new UserController();

// 3. Récupération de l'action
$action = $_GET['action'] ?? 'accueil';

// --- GESTION DES ACTIONS (SANS AFFICHAGE AVANT REDIRECTION) ---
switch($action) {

    case 'ajouter_panier':
            $homeController->addToCart();
            exit(); // On arrête tout car addToCart va rediriger

    case 'ajouter_activite_panier':
            $homeController->addActivityToCart();
            exit();

    case 'logout':
        $userController->logout();
        exit(); // Toujours exit après un logout
    
    case 'valider_bien':
        $adminController = new AdminController();
        $adminController->confirmerContrat();
        exit();

    case 'rejeter_contrat':
        $adminController = new AdminController();
        $adminController->rejeterContrat();
        exit();

    case 'ajouter_logement':
        $ownerController = new OwnerController();
        $ownerController->traiterAjoutLogement($_POST, $_FILES);
        exit();

    case 'supprimer_reservation':
        $homeController->supprimerReservation();
        exit();

    case 'supprimer_activite':
        $homeController->supprimerActivite();
        exit();

    case 'vider_logement':
        $homeController->viderLogement();
        exit();

    case 'confirmer_reservations':
        $homeController->confirmerToutesReservations();
        exit();
}

// --- AFFICHAGE ---
// On n'inclut le header que si on n'est pas en train de faire une redirection
include 'views/templates/header.php'; 



switch($action) {
    case 'accueil':
        $homeController->displayHome();
        break;

    case 'connexion':
        $userController->connexion();
        break;

    case 'apropos':
        $homeController->apropos();
        break;

    case 'dashboard_admin':
        $adminController = new AdminController();
        $adminController->displayDashboard();
        break;
        

    case 'modifier_logement':
        $ownerController = new OwnerController();
        $ownerController->modifierLogement();
        break;

    case 'supprimer_logement':
        $ownerController = new OwnerController();
        $ownerController->supprimerLogement();
        break;

    case 'details':
        $homeController->details();
        break;

    case 'panier':
        // On autorise l'accès au panier même s'il est vide
        $unModele = new Modele();
        include 'views/panier.php';
        break;    

    case 'destinations':
        $homeController->destinations();
        break;

    case 'edit_profile':
        $userController->editProfile();
        break;
    
    case 'dashboard':
        // Correction : Le contrôleur gère lui-même la session
        $ownerController = new OwnerController();
        $ownerController->displayDashboard();
        break;
    
    case 'profile':
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $adminController = new AdminController();
            $adminController->afficherProfil();
        } else {
            $userController->displayProfile();
        }
        break;
    
    case 'rechercher':
        $homeController->search();
        break; 
    
    case 'soumettre_avis':
        if (isset($_POST['id_res']) && isset($_POST['note']) && isset($_SESSION['id_user'])) {
            $modeleAvis = new Modele();
            $avisExistant = $modeleAvis->selectAvisByRes((int)$_POST['id_res']);
            if (!$avisExistant) {
                $modeleAvis->insertAvis((int)$_POST['id_res'], (int)$_POST['note'], trim($_POST['commentaire'] ?? ''));
                $_SESSION['flash_success'] = "Merci pour votre évaluation !";
            } else {
                $_SESSION['flash_error'] = "Vous avez déjà évalué ce séjour.";
            }
        }
        header("Location: index.php?action=profile");
        exit();

    default:
        $homeController->displayHome();
        break;
}

include 'views/templates/footer.php'; // Optionnel si tu as un footer