package controleur;

public class Habitation {

    private int    idHabit, nbChambres, nbVoyageurs, idPro;
    private String titre, ville, station, type, statut;
    private double prixNuit, surface;

    // Constructeur complet (lecture BDD)
    public Habitation(int idHabit, String titre, String ville, String station,
                      String type, double prixNuit, double surface,
                      int nbChambres, int nbVoyageurs, String statut, int idPro) {
        this.idHabit     = idHabit;
        this.titre       = titre;
        this.ville       = ville;
        this.station     = station;
        this.type        = type;
        this.prixNuit    = prixNuit;
        this.surface     = surface;
        this.nbChambres  = nbChambres;
        this.nbVoyageurs = nbVoyageurs;
        this.statut      = statut;
        this.idPro       = idPro;
    }

    // Getters
    public int    getIdHabit()     { return idHabit;     }
    public String getTitre()       { return titre;       }
    public String getVille()       { return ville;       }
    public String getStation()     { return station;     }
    public String getType()        { return type;        }
    public double getPrixNuit()    { return prixNuit;    }
    public double getSurface()     { return surface;     }
    public int    getNbChambres()  { return nbChambres;  }
    public int    getNbVoyageurs() { return nbVoyageurs; }
    public String getStatut()      { return statut;      }
    public int    getIdPro()       { return idPro;       }

    // Setters
    public void setIdHabit    (int    idHabit)     { this.idHabit     = idHabit;     }
    public void setTitre      (String titre)       { this.titre       = titre;       }
    public void setVille      (String ville)       { this.ville       = ville;       }
    public void setStation    (String station)     { this.station     = station;     }
    public void setType       (String type)        { this.type        = type;        }
    public void setPrixNuit   (double prixNuit)    { this.prixNuit    = prixNuit;    }
    public void setSurface    (double surface)     { this.surface     = surface;     }
    public void setNbChambres (int    nbChambres)  { this.nbChambres  = nbChambres;  }
    public void setNbVoyageurs(int    nbVoyageurs) { this.nbVoyageurs = nbVoyageurs; }
    public void setStatut     (String statut)      { this.statut      = statut;      }
    public void setIdPro      (int    idPro)       { this.idPro       = idPro;       }

    @Override
    public String toString() {
        return titre + " - " + ville + " (" + type + ")";
    }
}
