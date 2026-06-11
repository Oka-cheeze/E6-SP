package controleur;

import java.util.ArrayList;
import modele.modele;

public class Controleur {

    // ===========================================================
    //  AUTHENTIFICATION
    // ===========================================================

    public static User connecterAdmin(String email, String mdp) {
        // Contrôle basique : champs non vides
        if (email == null || email.trim().isEmpty()) return null;
        if (mdp   == null || mdp.trim().isEmpty())   return null;
        return modele.connecterAdmin(email.trim(), mdp.trim());
    }

    // ===========================================================
    //  UTILISATEURS
    // ===========================================================

    public static ArrayList<User> selectAllUsers(String filtre) {
        return modele.selectAllUsers(filtre == null ? "" : filtre.trim());
    }

    public static void insertUser(User u) {
        if (u.getNom().isEmpty() || u.getPrenom().isEmpty() || u.getEmail().isEmpty()) return;
        modele.insertUser(u);
    }

    public static void updateUser(User u) {
        if (u.getNom().isEmpty() || u.getPrenom().isEmpty() || u.getEmail().isEmpty()) return;
        modele.updateUser(u);
    }

    public static void deleteUser(int idUser) {
        modele.deleteUser(idUser);
    }

    // ===========================================================
    //  HABITATIONS
    // ===========================================================

    public static ArrayList<Habitation> selectAllHabitations(String filtre) {
        return modele.selectAllHabitations(filtre == null ? "" : filtre.trim());
    }

    public static void validerHabitation(int idHabit) {
        modele.validerHabitation(idHabit);
    }

    public static void rejeterHabitation(int idHabit, String motif) {
        if (motif == null || motif.trim().isEmpty()) motif = "Refusé par l'administrateur.";
        modele.rejeterHabitation(idHabit, motif.trim());
    }

    public static void deleteHabitation(int idHabit) {
        modele.deleteHabitation(idHabit);
    }

    public static void updateStatutHabitation(int idHabit, String statut) {
        modele.updateStatutHabitation(idHabit, statut);
    }

    // ===========================================================
    //  RÉSERVATIONS
    // ===========================================================

    public static ArrayList<Reservation> selectAllReservations(String filtre) {
        return modele.selectAllReservations(filtre == null ? "" : filtre.trim());
    }

    public static void updateStatutReservation(int idRes, String statut) {
        modele.updateStatutReservation(idRes, statut);
    }

    public static void deleteReservation(int idRes) {
        modele.deleteReservation(idRes);
    }

    // ===========================================================
    //  STATISTIQUES
    // ===========================================================

    public static Object[][] getStatsParStation() {
        return modele.selectStatistiquesParStation();
    }

    public static Object[][] getStatsParType() {
        return modele.selectStatistiquesParType();
    }
}
