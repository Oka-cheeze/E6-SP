package controleur;

import vue.VueConnexion;
import vue.VueGeneral;

public class NeigeEtSoleil {

    private static VueConnexion uneVueConnexion;
    private static VueGeneral   uneVueGeneral;
    private static User         adminConnecte;

    public static void main(String[] args) {
        // On lance uniquement la fenêtre de connexion au démarrage
        uneVueConnexion = new VueConnexion();
    }

    // -------------------------------------------------------
    //  Contrôle de visibilité des fenêtres
    // -------------------------------------------------------

    public static void rendreVisibleVueConnexion(boolean visible) {
        if (uneVueConnexion != null) {
            uneVueConnexion.setVisible(visible);
        }
    }

    public static void creerDetruireVueGeneral(boolean creer) {
        if (creer) {
            uneVueGeneral = new VueGeneral();
        } else {
            if (uneVueGeneral != null) {
                uneVueGeneral.dispose();
                uneVueGeneral = null;
            }
        }
    }

    // -------------------------------------------------------
    //  Accès à l'admin connecté
    // -------------------------------------------------------

    public static User getAdminConnecte() {
        return adminConnecte;
    }

    public static void setAdminConnecte(User admin) {
        adminConnecte = admin;
    }
}
