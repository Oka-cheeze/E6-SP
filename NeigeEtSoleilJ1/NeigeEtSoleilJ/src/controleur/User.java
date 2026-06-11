package controleur;

public class User {

    private int    idUser;
    private String nom, prenom, email, role, tel, adresse, cp, ville;

    // Constructeur complet (lecture BDD)
    public User(int idUser, String nom, String prenom, String email,
                String role, String tel, String adresse, String cp, String ville) {
        this.idUser  = idUser;
        this.nom     = nom;
        this.prenom  = prenom;
        this.email   = email;
        this.role    = role;
        this.tel     = tel;
        this.adresse = adresse;
        this.cp      = cp;
        this.ville   = ville;
    }

    // Constructeur sans id (insertion)
    public User(String nom, String prenom, String email,
                String role, String tel, String adresse, String cp, String ville) {
        this(0, nom, prenom, email, role, tel, adresse, cp, ville);
    }

    // Getters
    public int    getIdUser()  { return idUser;  }
    public String getNom()     { return nom;     }
    public String getPrenom()  { return prenom;  }
    public String getEmail()   { return email;   }
    public String getRole()    { return role;    }
    public String getTel()     { return tel;     }
    public String getAdresse() { return adresse; }
    public String getCp()      { return cp;      }
    public String getVille()   { return ville;   }

    // Setters
    public void setIdUser (int    idUser)  { this.idUser  = idUser;  }
    public void setNom    (String nom)     { this.nom     = nom;     }
    public void setPrenom (String prenom)  { this.prenom  = prenom;  }
    public void setEmail  (String email)   { this.email   = email;   }
    public void setRole   (String role)    { this.role    = role;    }
    public void setTel    (String tel)     { this.tel     = tel;     }
    public void setAdresse(String adresse) { this.adresse = adresse; }
    public void setCp     (String cp)      { this.cp      = cp;      }
    public void setVille  (String ville)   { this.ville   = ville;   }

    @Override
    public String toString() {
        return nom + " " + prenom + " (" + role + ")";
    }
}
