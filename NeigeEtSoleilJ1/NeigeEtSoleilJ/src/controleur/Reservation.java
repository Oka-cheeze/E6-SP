package controleur;

public class Reservation {

    private int    idRes, nbPersonnes;
    private String nomClient, titreHabit, dateDebut, dateFin, statut;
    private double prixTotal;

    // Constructeur complet (lecture BDD)
    public Reservation(int idRes, String nomClient, String titreHabit,
                       String dateDebut, String dateFin,
                       int nbPersonnes, double prixTotal, String statut) {
        this.idRes      = idRes;
        this.nomClient  = nomClient;
        this.titreHabit = titreHabit;
        this.dateDebut  = dateDebut;
        this.dateFin    = dateFin;
        this.nbPersonnes = nbPersonnes;
        this.prixTotal  = prixTotal;
        this.statut     = statut;
    }

    // Getters
    public int    getIdRes()      { return idRes;      }
    public String getNomClient()  { return nomClient;  }
    public String getTitreHabit() { return titreHabit; }
    public String getDateDebut()  { return dateDebut;  }
    public String getDateFin()    { return dateFin;    }
    public int    getNbPersonnes(){ return nbPersonnes; }
    public double getPrixTotal()  { return prixTotal;  }
    public String getStatut()     { return statut;     }

    // Setters
    public void setIdRes      (int    idRes)       { this.idRes      = idRes;      }
    public void setNomClient  (String nomClient)   { this.nomClient  = nomClient;  }
    public void setTitreHabit (String titreHabit)  { this.titreHabit = titreHabit; }
    public void setDateDebut  (String dateDebut)   { this.dateDebut  = dateDebut;  }
    public void setDateFin    (String dateFin)     { this.dateFin    = dateFin;    }
    public void setNbPersonnes(int    nbPersonnes) { this.nbPersonnes = nbPersonnes; }
    public void setPrixTotal  (double prixTotal)   { this.prixTotal  = prixTotal;  }
    public void setStatut     (String statut)      { this.statut     = statut;     }

    @Override
    public String toString() {
        return "Résa #" + idRes + " - " + nomClient + " (" + titreHabit + ")";
    }
}
