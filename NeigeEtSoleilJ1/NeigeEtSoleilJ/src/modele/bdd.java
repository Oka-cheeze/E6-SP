package modele;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class bdd {

    private String serveur, base, user, mdp;
    private Connection maConnexion;

    public bdd(String serveur, String base, String user, String mdp) {
        this.serveur      = serveur;
        this.base         = base;
        this.user         = user;
        this.mdp          = mdp;
        this.maConnexion  = null;
    }

    public void chargerPilote() {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            System.out.println("Pilote JDBC introuvable : " + e.getMessage());
        }
    }

    public void seConnecter() {
        this.chargerPilote();
        String url = "jdbc:mysql://" + this.serveur + "/" + this.base
                   + "?useUnicode=true&characterEncoding=UTF-8&serverTimezone=Europe/Paris";
        try {
            this.maConnexion = DriverManager.getConnection(url, this.user, this.mdp);
        } catch (SQLException e) {
            System.out.println("Impossible de se connecter à : " + url);
            System.out.println(e.getMessage());
        }
    }

    public void seDeconnecter() {
        try {
            if (this.maConnexion != null && !this.maConnexion.isClosed()) {
                this.maConnexion.close();
            }
        } catch (SQLException e) {
            System.out.println("Erreur lors de la déconnexion : " + e.getMessage());
        }
    }

    public Connection getMaConnexion() {
        return this.maConnexion;
    }
}
