<?php
require_once ("modele/modele.class.php");

class UserController {
    private $unModele;

    public function __construct() {
        $this->unModele = new Modele();
    }

    public function displayProfile() {
        if (!isset($_SESSION['id_user'])) {
            header("Location: index.php?action=connexion");
            exit();
        }

        $id_user = $_SESSION['id_user'];
        $role = $_SESSION['role'];

        if ($role === 'proprietaire') {
            $data = $this->unModele->selectProprietaireById($id_user);
        } else {
            $data = $this->unModele->selectClientById($id_user);
        }
        
        // On renomme $history en $lesReservations pour correspondre à la vue
        $lesReservations = $this->unModele->selectReservationsByClient($id_user); 
        
        $user = $data;
        include("views/profile_client.php");
    }

    public function editProfile() {
        if (!isset($_SESSION['id_user'])) {
            header("Location: index.php?action=connexion");
            exit();
        }

        if (isset($_POST['valider_modif'])) {
            // On récupère les données du formulaire de la modale
            $tab = [
                'id_user' => $_SESSION['id_user'],
                'role'    => $_SESSION['role'],
                'nom'     => $_POST['nom'],
                'prenom'  => $_POST['prenom'],
                'adresse' => $_POST['adresse'],
                'date_n'  => $_POST['date_n'] ?? null // Si c'est un client
            ];

            // On appelle ta fonction de mise à jour
            $this->unModele->updateUser($tab);

            header("Location: index.php?action=profile&success=updated");
            exit();
        }
    }

    public function connexion() {
        $error_login = null;
        $error_inscription = null;

        // --- 1. TRAITEMENT DE LA CONNEXION ---
        if (isset($_POST['valider_connexion'])) {
            $email = $_POST['email'];
            $mdp   = $_POST['mdp'];
            
            $user = $this->unModele->verifConnexion($email, $mdp);
            
            if ($user) {
                $_SESSION['id_user'] = $user['ID_USER'];
                $_SESSION['nom']     = $user['NOM_USER'];
                $_SESSION['prenom']  = $user['PRENOM_USER'];
                $_SESSION['role']    = $user['ROLE_USER'];

                if ($_SESSION['role'] === 'admin') {
                    header("Location: index.php?action=dashboard_admin");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error_login = "Identifiants incorrects. Veuillez réessayer.";
            }
        }

        // --- 2. TRAITEMENT DE L'INSCRIPTION ---
        if (isset($_POST['valider_inscription'])) {
            $tab = $_POST; 
            $res = $this->unModele->inscriptionUser($tab); 
            
            if ($res) {
                header("Location: index.php?action=connexion&success=inscrit");
                exit();
            } else {
                $error_inscription = "Erreur lors de l'inscription. L'email est peut-être déjà utilisé.";
            }
        }

        // Affichage de la vue de connexion (une seule fois)
        include("views/connexion.php");
    }

    public function logout() {
        $panier = $_SESSION['panier'] ?? null;
        $isEmpty = empty($panier['reservations']); // Vérifie si le tableau de résas est vide
        
        session_destroy();
        session_start();
        
        if (!$isEmpty) {
            $_SESSION['panier'] = $panier;
            $_SESSION['flash_success'] = "Déconnexion réussie. Votre panier est conservé.";
        }
        
        header("Location: index.php"); exit();
    }
}