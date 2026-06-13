<?php
require_once ("modele/modele.class.php");

class AdminController {
    private $unModele;

    public function __construct() {
        // Sécurité : Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php");
            exit();
        }
        $this->unModele = new Modele();
    }

    public function afficherProfil() {
        $id_user = $_SESSION['id_user'];
        // Remplace $infosAdmin par $user
        $user = $this->unModele->selectUserById($id_user); 
        
        include("views/profile_client.php"); 
    }

    public function displayDashboard() {
        $en_attente = $this->unModele->selectHabitationsEnAttente();
        $unModele = $this->unModele; // Expose le modèle à la vue
        include("views/dashboard_admin.php");
    }

    public function confirmerContrat() {
        if (isset($_GET['id_habit'])) {
            $this->unModele->validerHabitation($_GET['id_habit']);
            // Correction : on utilise dashboard_admin (le nom dans index.php)
            header("Location: index.php?action=dashboard_admin&status=valide");
            exit();
        }
    }

    public function rejeterContrat() {
        // On vérifie le POST envoyé par le formulaire/JS du dashboard
        if (isset($_POST['id_habit']) && !empty($_POST['motif'])) {
            $this->unModele->refuserHabitationAvecMotif($_POST['id_habit'], $_POST['motif']);
            header("Location: index.php?action=dashboard_admin&status=rejete");
            exit();
        }   
    }
}