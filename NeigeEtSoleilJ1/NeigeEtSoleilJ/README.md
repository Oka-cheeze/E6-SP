# Neige et Soleil — Interface Client Lourd Java
## Guide d'installation et de configuration Eclipse

---

## 1. Structure du projet

```
NeigeEtSoleil/
├── src/
│   ├── controleur/
│   │   ├── NeigeEtSoleil.java     ← Point d'entrée (main)
│   │   ├── Controleur.java        ← Méthodes statiques MVC
│   │   ├── User.java              ← Entité utilisateur
│   │   ├── Habitation.java        ← Entité habitation
│   │   ├── Reservation.java       ← Entité réservation
│   │   └── Tableau.java           ← Modèle de données JTable
│   ├── modele/
│   │   ├── bdd.java               ← Connexion JDBC MySQL
│   │   └── modele.java            ← Toutes les requêtes SQL
│   ├── vue/
│   │   ├── VueConnexion.java      ← Fenêtre de connexion
│   │   ├── VueGeneral.java        ← Fenêtre principale + menu
│   │   ├── PanelPrincipal.java    ← Classe abstraite parente
│   │   ├── PanelProfil.java       ← Onglet profil admin
│   │   ├── PanelUsers.java        ← Onglet gestion utilisateurs
│   │   ├── PanelHabitations.java  ← Onglet gestion logements
│   │   ├── PanelReservations.java ← Onglet gestion réservations
│   │   └── PanelStats.java        ← Onglet statistiques
│   └── images/
│       └── logo.png               ← Logo Neige et Soleil
├── lib/
│   └── (mettre ici le .jar MySQL) ← Voir étape 3
├── .classpath                     ← Config Eclipse
└── .project                       ← Config Eclipse
```

---

## 2. Importer le projet dans Eclipse

1. Ouvrir Eclipse
2. `File` → `Import` → `General` → `Existing Projects into Workspace`
3. Cliquer sur `Browse...` et sélectionner le dossier `NeigeEtSoleil/`
4. Cocher le projet détecté → `Finish`

---

## 3. Ajouter le driver MySQL (JDBC)

Le projet nécessite le connecteur JDBC MySQL.

**Téléchargement :**
- Aller sur : https://dev.mysql.com/downloads/connector/j/
- Choisir "Platform Independent" → télécharger le `.zip`
- Extraire et récupérer le fichier `mysql-connector-j-X.X.X.jar`

**Ajout dans Eclipse :**
1. Copier le `.jar` dans le dossier `lib/` du projet
2. Dans Eclipse : clic droit sur le projet → `Build Path` → `Configure Build Path`
3. Onglet `Libraries` → `Add JARs...`
4. Naviguer vers `lib/` → sélectionner le `.jar` → `OK`
5. `Apply and Close`

**Alternative rapide :**
- Clic droit sur le `.jar` dans l'explorateur Eclipse → `Build Path` → `Add to Build Path`

---

## 4. Configurer la connexion à la base de données

Ouvrir `src/modele/bdd.java` et vérifier/modifier cette ligne :

```java
private static bdd uneBdd = new bdd("localhost", "neige_et_soleilPPE", "root", "");
```

| Paramètre | Valeur par défaut | À modifier si... |
|-----------|-------------------|-----------------|
| Serveur   | `localhost`       | BDD sur un autre serveur |
| Base      | `neige_et_soleilPPE` | Nom différent dans phpMyAdmin |
| User      | `root`            | Autre utilisateur MySQL |
| Mot de passe | `""` (vide)    | Vous avez un mot de passe MySQL |

---

## 5. Importer la base de données

1. Ouvrir **phpMyAdmin** ou **MySQL Workbench**
2. Créer (ou laisser le script créer) la base `neige_et_soleilPPE`
3. Importer le fichier `neigeEtSoleilPPE.sql`
4. Vérifier que les tables sont bien créées

**Compte administrateur de test (créé par le script SQL) :**
- Email    : `admin@neige.fr`
- Mot de passe : `admin123`

---

## 6. Lancer l'application

1. Dans Eclipse, ouvrir `src/controleur/NeigeEtSoleil.java`
2. Clic droit → `Run As` → `Java Application`
3. La fenêtre de connexion s'ouvre
4. Saisir `admin@neige.fr` / `admin123`

---

## 7. Fonctionnalités de l'interface

| Onglet | Fonctionnalités |
|--------|----------------|
| **Mon profil** | Affiche les infos de l'admin connecté |
| **Utilisateurs** | Lister, rechercher, ajouter, modifier, supprimer des utilisateurs |
| **Habitations** | Lister tous les logements, valider/rejeter les contrats en attente, supprimer |
| **Réservations** | Lister, confirmer, annuler, supprimer des réservations |
| **Statistiques** | Prix min/max/moyen par station et par type de logement |

---

## 8. Architecture MVC (même pattern qu'OrangeEvent)

```
OrangeEvent          →    NeigeEtSoleil
─────────────────────────────────────────────────────
OrangeEvent.java     →    NeigeEtSoleil.java  (main)
Controleur.java      →    Controleur.java
Technicien.java      →    User.java
Client.java          →    Habitation.java
Objet.java           →    Reservation.java
Tableau.java         →    Tableau.java        (identique)
modele/bdd.java      →    modele/bdd.java     (identique)
modele/modele.java   →    modele/modele.java
VueConnexion.java    →    VueConnexion.java
VueGeneral.java      →    VueGeneral.java     (menu latéral)
PanelPrincipal.java  →    PanelPrincipal.java (classe abstraite)
PanelProfil.java     →    PanelProfil.java
PanelClient.java     →    PanelUsers.java
PanelTechnicien.java →    PanelHabitations.java
PanelObjets.java     →    PanelReservations.java
PanelStats.java      →    PanelStats.java
```

---

## 9. Erreurs fréquentes

| Erreur | Cause | Solution |
|--------|-------|----------|
| `ClassNotFoundException: com.mysql.cj.jdbc.Driver` | JAR MySQL absent | Ajouter le JAR au Build Path (étape 3) |
| `Impossible de se connecter` | BDD éteinte ou mauvais credentials | Vérifier XAMPP/MySQL actif + `bdd.java` |
| `Table 'neige_et_soleilPPE.USER' doesn't exist` | SQL non importé | Importer `neigeEtSoleilPPE.sql` |
| `NullPointerException` sur `adminConnecte` | Connexion échouée | Vérifier email/mdp dans `VueConnexion` |
