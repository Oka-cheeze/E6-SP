package modele;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;

import controleur.Habitation;
import controleur.Reservation;
import controleur.User;

public class modele {

    // -------------------------------------------------------
    //  Connexion à la base neige_et_soleilPPE
    //  Modifier "localhost" / "root" / "" selon votre config
    // -------------------------------------------------------
    private static bdd uneBdd = new bdd("localhost", "neige_et_soleilPPE", "root", "");

    // -------------------------------------------------------
    //  Utilitaire interne : exécuter INSERT / UPDATE / DELETE
    // -------------------------------------------------------
    private static void executerRequete(String requete) {
        try {
            uneBdd.seConnecter();
            Statement unStat = uneBdd.getMaConnexion().createStatement();
            unStat.execute(requete);
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur requête : " + requete);
            System.out.println(e.getMessage());
        }
    }

    // ===========================================================
    //  AUTHENTIFICATION
    // ===========================================================

    /**
     * Vérifie les identifiants d'un admin.
     * Retourne un User si trouvé, null sinon.
     */
    public static User connecterAdmin(String email, String mdp) {
        User unUser = null;
        String requete = "SELECT u.* FROM USER u "
                       + "JOIN ADMINISTRATEUR a ON u.ID_USER = a.ID_USER "
                       + "WHERE u.EMAIL_USER = '" + email + "' "
                       + "AND u.MDP_USER = '" + mdp + "';";
        try {
            uneBdd.seConnecter();
            Statement unStat = uneBdd.getMaConnexion().createStatement();
            ResultSet res    = unStat.executeQuery(requete);
            if (res.next()) {
                unUser = new User(
                    res.getInt("ID_USER"),
                    res.getString("NOM_USER"),
                    res.getString("PRENOM_USER"),
                    res.getString("EMAIL_USER"),
                    res.getString("ROLE_USER"),
                    res.getString("TEL_USER"),
                    res.getString("ADRESSE_USER"),
                    res.getString("CP_USER"),
                    res.getString("VILLE_USER")
                );
            }
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur connexion admin : " + e.getMessage());
        }
        return unUser;
    }

    // ===========================================================
    //  GESTION DES UTILISATEURS
    // ===========================================================

    public static ArrayList<User> selectAllUsers(String filtre) {
        String requete;
        if (filtre.equals("")) {
            requete = "SELECT * FROM USER ORDER BY NOM_USER;";
        } else {
            requete = "SELECT * FROM USER WHERE "
                    + "NOM_USER    LIKE '%" + filtre + "%' OR "
                    + "PRENOM_USER LIKE '%" + filtre + "%' OR "
                    + "EMAIL_USER  LIKE '%" + filtre + "%' OR "
                    + "VILLE_USER  LIKE '%" + filtre + "%' OR "
                    + "ROLE_USER   LIKE '%" + filtre + "%' "
                    + "ORDER BY NOM_USER;";
        }
        ArrayList<User> lesUsers = new ArrayList<>();
        try {
            uneBdd.seConnecter();
            Statement unStat     = uneBdd.getMaConnexion().createStatement();
            ResultSet desResultats = unStat.executeQuery(requete);
            while (desResultats.next()) {
                User u = new User(
                    desResultats.getInt("ID_USER"),
                    desResultats.getString("NOM_USER"),
                    desResultats.getString("PRENOM_USER"),
                    desResultats.getString("EMAIL_USER"),
                    desResultats.getString("ROLE_USER"),
                    desResultats.getString("TEL_USER"),
                    desResultats.getString("ADRESSE_USER"),
                    desResultats.getString("CP_USER"),
                    desResultats.getString("VILLE_USER")
                );
                lesUsers.add(u);
            }
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur selectAllUsers : " + e.getMessage());
        }
        return lesUsers;
    }

    public static void insertUser(User u) {
        String requete = "INSERT INTO USER (NOM_USER, PRENOM_USER, EMAIL_USER, MDP_USER, "
                       + "TEL_USER, ADRESSE_USER, CP_USER, VILLE_USER, ROLE_USER) VALUES ("
                       + "'" + u.getNom()     + "', "
                       + "'" + u.getPrenom()  + "', "
                       + "'" + u.getEmail()   + "', "
                       + "'changeme', "
                       + "'" + (u.getTel()     != null ? u.getTel()     : "") + "', "
                       + "'" + (u.getAdresse() != null ? u.getAdresse() : "") + "', "
                       + "'" + (u.getCp()      != null ? u.getCp()      : "") + "', "
                       + "'" + (u.getVille()   != null ? u.getVille()   : "") + "', "
                       + "'" + u.getRole()    + "');";
        executerRequete(requete);
    }

    public static void updateUser(User u) {
        String requete = "UPDATE USER SET "
                       + "NOM_USER     = '" + u.getNom()     + "', "
                       + "PRENOM_USER  = '" + u.getPrenom()  + "', "
                       + "EMAIL_USER   = '" + u.getEmail()   + "', "
                       + "TEL_USER     = '" + (u.getTel()     != null ? u.getTel()     : "") + "', "
                       + "ADRESSE_USER = '" + (u.getAdresse() != null ? u.getAdresse() : "") + "', "
                       + "CP_USER      = '" + (u.getCp()      != null ? u.getCp()      : "") + "', "
                       + "VILLE_USER   = '" + (u.getVille()   != null ? u.getVille()   : "") + "', "
                       + "ROLE_USER    = '" + u.getRole()    + "' "
                       + "WHERE ID_USER = " + u.getIdUser() + ";";
        executerRequete(requete);
    }

    public static void deleteUser(int idUser) {
        executerRequete("DELETE FROM USER WHERE ID_USER = " + idUser + ";");
    }

    // ===========================================================
    //  GESTION DES HABITATIONS
    // ===========================================================

    public static ArrayList<Habitation> selectAllHabitations(String filtre) {
        String requete;
        if (filtre.equals("")) {
            requete = "SELECT H.*, S.NOM_STATION, "
                    + "CASE WHEN A.ID_HABIT IS NOT NULL THEN 'Appartement' "
                    + "     WHEN M.ID_HABIT IS NOT NULL THEN 'Maison' "
                    + "     WHEN C.ID_HABIT IS NOT NULL THEN 'Chalet' "
                    + "     ELSE 'Inconnu' END AS TYPE_LOGEMENT "
                    + "FROM HABITATION H "
                    + "LEFT JOIN STATION     S ON H.ID_STATION = S.ID_STATION "
                    + "LEFT JOIN APPARTEMENT A ON H.ID_HABIT   = A.ID_HABIT "
                    + "LEFT JOIN MAISON      M ON H.ID_HABIT   = M.ID_HABIT "
                    + "LEFT JOIN CHALET      C ON H.ID_HABIT   = C.ID_HABIT "
                    + "ORDER BY H.ID_HABIT;";
        } else {
            requete = "SELECT H.*, S.NOM_STATION, "
                    + "CASE WHEN A.ID_HABIT IS NOT NULL THEN 'Appartement' "
                    + "     WHEN M.ID_HABIT IS NOT NULL THEN 'Maison' "
                    + "     WHEN C.ID_HABIT IS NOT NULL THEN 'Chalet' "
                    + "     ELSE 'Inconnu' END AS TYPE_LOGEMENT "
                    + "FROM HABITATION H "
                    + "LEFT JOIN STATION     S ON H.ID_STATION = S.ID_STATION "
                    + "LEFT JOIN APPARTEMENT A ON H.ID_HABIT   = A.ID_HABIT "
                    + "LEFT JOIN MAISON      M ON H.ID_HABIT   = M.ID_HABIT "
                    + "LEFT JOIN CHALET      C ON H.ID_HABIT   = C.ID_HABIT "
                    + "WHERE H.TITRE_HABIT   LIKE '%" + filtre + "%' OR "
                    + "      H.VILLE_HABIT   LIKE '%" + filtre + "%' OR "
                    + "      H.STATUT_HABIT  LIKE '%" + filtre + "%' OR "
                    + "      S.NOM_STATION   LIKE '%" + filtre + "%' "
                    + "ORDER BY H.ID_HABIT;";
        }
        ArrayList<Habitation> lesHabitations = new ArrayList<>();
        try {
            uneBdd.seConnecter();
            Statement  unStat      = uneBdd.getMaConnexion().createStatement();
            ResultSet  desResultats = unStat.executeQuery(requete);
            while (desResultats.next()) {
                Habitation h = new Habitation(
                    desResultats.getInt("ID_HABIT"),
                    desResultats.getString("TITRE_HABIT"),
                    desResultats.getString("VILLE_HABIT"),
                    desResultats.getString("NOM_STATION"),
                    desResultats.getString("TYPE_LOGEMENT"),
                    desResultats.getDouble("PRIX_NUIT_HABIT"),
                    desResultats.getDouble("SURFACE_HABIT"),
                    desResultats.getInt("NB_CHAMBRES_HABIT"),
                    desResultats.getInt("NB_GUETS_HABIT"),
                    desResultats.getString("STATUT_HABIT"),
                    desResultats.getInt("ID_PRO")
                );
                lesHabitations.add(h);
            }
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur selectAllHabitations : " + e.getMessage());
        }
        return lesHabitations;
    }

    public static void validerHabitation(int idHabit) {
        // Met à jour le contrat → le trigger SQL passe STATUT_HABIT à 'disponible'
        executerRequete("UPDATE CONTRAT SET STATUT_CONTRAT = 'valide' "
                      + "WHERE ID_HABIT = " + idHabit + " AND STATUT_CONTRAT = 'en_attente';");
    }

    public static void rejeterHabitation(int idHabit, String motif) {
        executerRequete("UPDATE CONTRAT SET STATUT_CONTRAT = 'refuse', "
                      + "MOTIF_REFUS_CONTRAT = '" + motif.replace("'", "''") + "' "
                      + "WHERE ID_HABIT = " + idHabit + " AND STATUT_CONTRAT = 'en_attente';");
        executerRequete("UPDATE HABITATION SET STATUT_HABIT = 'rejete' "
                      + "WHERE ID_HABIT = " + idHabit + ";");
    }

    public static void deleteHabitation(int idHabit) {
        executerRequete("DELETE FROM HABITATION WHERE ID_HABIT = " + idHabit + ";");
    }

    public static void updateStatutHabitation(int idHabit, String statut) {
        executerRequete("UPDATE HABITATION SET STATUT_HABIT = '" + statut + "' "
                      + "WHERE ID_HABIT = " + idHabit + ";");
    }

    // ===========================================================
    //  GESTION DES RÉSERVATIONS
    // ===========================================================

    public static ArrayList<Reservation> selectAllReservations(String filtre) {
        String requete;
        if (filtre.equals("")) {
            requete = "SELECT R.*, "
                    + "CONCAT(U.NOM_USER, ' ', U.PRENOM_USER) AS NOM_CLIENT, "
                    + "H.TITRE_HABIT "
                    + "FROM RESERVATION R "
                    + "JOIN USER       U ON R.ID_USER  = U.ID_USER "
                    + "JOIN HABITATION H ON R.ID_HABIT = H.ID_HABIT "
                    + "ORDER BY R.DATE_RES DESC;";
        } else {
            requete = "SELECT R.*, "
                    + "CONCAT(U.NOM_USER, ' ', U.PRENOM_USER) AS NOM_CLIENT, "
                    + "H.TITRE_HABIT "
                    + "FROM RESERVATION R "
                    + "JOIN USER       U ON R.ID_USER  = U.ID_USER "
                    + "JOIN HABITATION H ON R.ID_HABIT = H.ID_HABIT "
                    + "WHERE CONCAT(U.NOM_USER, ' ', U.PRENOM_USER) LIKE '%" + filtre + "%' OR "
                    + "      H.TITRE_HABIT  LIKE '%" + filtre + "%' OR "
                    + "      R.STATUT_RES   LIKE '%" + filtre + "%' "
                    + "ORDER BY R.DATE_RES DESC;";
        }
        ArrayList<Reservation> lesReservations = new ArrayList<>();
        try {
            uneBdd.seConnecter();
            Statement  unStat      = uneBdd.getMaConnexion().createStatement();
            ResultSet  desResultats = unStat.executeQuery(requete);
            while (desResultats.next()) {
                Reservation r = new Reservation(
                    desResultats.getInt("ID_RES"),
                    desResultats.getString("NOM_CLIENT"),
                    desResultats.getString("TITRE_HABIT"),
                    desResultats.getString("DATE_DEBUT_RES"),
                    desResultats.getString("DATE_FIN_RES"),
                    desResultats.getInt("NB_PERSONNES"),
                    desResultats.getDouble("PRIX_TOTAL_RES"),
                    desResultats.getString("STATUT_RES")
                );
                lesReservations.add(r);
            }
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur selectAllReservations : " + e.getMessage());
        }
        return lesReservations;
    }

    public static void updateStatutReservation(int idRes, String statut) {
        executerRequete("UPDATE RESERVATION SET STATUT_RES = '" + statut + "' "
                      + "WHERE ID_RES = " + idRes + ";");
    }

    public static void deleteReservation(int idRes) {
        executerRequete("DELETE FROM RESERVATION WHERE ID_RES = " + idRes + ";");
    }

    // ===========================================================
    //  STATISTIQUES
    // ===========================================================

    /**
     * Retourne une matrice [station][prix_min, prix_max, prix_moyen, nb_logements]
     * pour affichage dans PanelStats.
     */
    public static Object[][] selectStatistiquesParStation() {
        String requete = "SELECT S.NOM_STATION, "
                       + "MIN(H.PRIX_NUIT_HABIT) AS PRIX_MIN, "
                       + "MAX(H.PRIX_NUIT_HABIT) AS PRIX_MAX, "
                       + "ROUND(AVG(H.PRIX_NUIT_HABIT), 2) AS PRIX_MOY, "
                       + "COUNT(H.ID_HABIT) AS NB_LOGEMENTS "
                       + "FROM HABITATION H "
                       + "JOIN STATION S ON H.ID_STATION = S.ID_STATION "
                       + "GROUP BY S.NOM_STATION "
                       + "ORDER BY S.NOM_STATION;";
        ArrayList<Object[]> lignes = new ArrayList<>();
        try {
            uneBdd.seConnecter();
            Statement unStat      = uneBdd.getMaConnexion().createStatement();
            ResultSet desResultats = unStat.executeQuery(requete);
            while (desResultats.next()) {
                Object[] ligne = {
                    desResultats.getString("NOM_STATION"),
                    desResultats.getDouble("PRIX_MIN")  + " €",
                    desResultats.getDouble("PRIX_MAX")  + " €",
                    desResultats.getDouble("PRIX_MOY")  + " €",
                    desResultats.getInt("NB_LOGEMENTS")
                };
                lignes.add(ligne);
            }
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur selectStatistiquesParStation : " + e.getMessage());
        }
        return lignes.toArray(new Object[0][]);
    }

    public static Object[][] selectStatistiquesParType() {
        String requete = "SELECT TYPE_LOGEMENT, "
                       + "MIN(PRIX_NUIT_HABIT) AS PRIX_MIN, "
                       + "MAX(PRIX_NUIT_HABIT) AS PRIX_MAX, "
                       + "ROUND(AVG(PRIX_NUIT_HABIT), 2) AS PRIX_MOY, "
                       + "COUNT(*) AS NB "
                       + "FROM V_CATALOGUE_BIENS "
                       + "GROUP BY TYPE_LOGEMENT;";
        ArrayList<Object[]> lignes = new ArrayList<>();
        try {
            uneBdd.seConnecter();
            Statement unStat      = uneBdd.getMaConnexion().createStatement();
            ResultSet desResultats = unStat.executeQuery(requete);
            while (desResultats.next()) {
                Object[] ligne = {
                    desResultats.getString("TYPE_LOGEMENT"),
                    desResultats.getDouble("PRIX_MIN") + " €",
                    desResultats.getDouble("PRIX_MAX") + " €",
                    desResultats.getDouble("PRIX_MOY") + " €",
                    desResultats.getInt("NB")
                };
                lignes.add(ligne);
            }
            unStat.close();
            uneBdd.seDeconnecter();
        } catch (SQLException e) {
            System.out.println("Erreur selectStatistiquesParType : " + e.getMessage());
        }
        return lignes.toArray(new Object[0][]);
    }
}
