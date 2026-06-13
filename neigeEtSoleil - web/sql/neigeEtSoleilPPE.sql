-- NEIGE ET SOLEIL


DROP DATABASE IF EXISTS neige_et_soleilPPE;
CREATE DATABASE IF NOT EXISTS neige_et_soleilPPE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE neige_et_soleilPPE;


-- BLOC 1 : UTILISATEURS (Héritage USER)


CREATE TABLE USER (
    ID_USER     INT NOT NULL AUTO_INCREMENT,
    NOM_USER    VARCHAR(50),
    PRENOM_USER VARCHAR(50),
    AGE_USER    INT,
    TEL_USER    VARCHAR(15),
    ADRESSE_USER VARCHAR(255),
    CP_USER     VARCHAR(5),
    VILLE_USER  VARCHAR(50),
    EMAIL_USER  VARCHAR(100) UNIQUE,
    MDP_USER    VARCHAR(255),
    ROLE_USER   ENUM('client', 'proprietaire', 'admin'),
    PRIMARY KEY (ID_USER)
);

CREATE TABLE CLIENT (
    ID_USER             INT NOT NULL,
    DATE_NAISSANCE_CLI  DATE,
    PRIMARY KEY (ID_USER),
    FOREIGN KEY (ID_USER) REFERENCES USER(ID_USER) ON DELETE CASCADE
);

CREATE TABLE PROPRIETAIRE (
    ID_USER          INT NOT NULL,
    RIB_PRO          VARCHAR(34),
    DATE_INSCRIPTION DATE,
    PRIMARY KEY (ID_USER),
    FOREIGN KEY (ID_USER) REFERENCES USER(ID_USER) ON DELETE CASCADE
);

CREATE TABLE ADMINISTRATEUR (
    ID_USER      INT NOT NULL,
    POSTE_ADMIN  VARCHAR(50),
    NIVEAU_ACCES INT,
    PRIMARY KEY (ID_USER),
    FOREIGN KEY (ID_USER) REFERENCES USER(ID_USER) ON DELETE CASCADE
);


-- BLOC 2 : GÉOGRAPHIE ET LOGEMENTS


CREATE TABLE REGION (
    ID_REG  INT NOT NULL AUTO_INCREMENT,
    NOM_REG VARCHAR(50),
    DEP_REG VARCHAR(50),
    PRIMARY KEY (ID_REG)
);

CREATE TABLE STATION (
    ID_STATION   INT NOT NULL AUTO_INCREMENT,
    ID_REG       INT NOT NULL,
    NOM_STATION  VARCHAR(50),
    NB_HABITANTS BIGINT,
    TEL_MAIRIE   VARCHAR(15),
    PRIMARY KEY (ID_STATION),
    FOREIGN KEY (ID_REG) REFERENCES REGION(ID_REG)
);

CREATE TABLE HABITATION (
    ID_HABIT         INT NOT NULL AUTO_INCREMENT,
    ID_PRO           INT NOT NULL,
    ID_STATION       INT NOT NULL,
    ID_REG           INT NOT NULL,
    TITRE_HABIT      VARCHAR(100),
    DESCRIPTION_HABIT TEXT,
    SURFACE_HABIT    DECIMAL(10,2),
    NB_CHAMBRES_HABIT INT,
    ADRESSE_HABIT    VARCHAR(255),
    CP_HABIT         VARCHAR(5),
    VILLE_HABIT      VARCHAR(50),
    IMAGE_HABIT      VARCHAR(255),
    NB_GUETS_HABIT   INT DEFAULT 2,
    NB_LITS_HABIT    INT DEFAULT 1,
    PRIX_NUIT_HABIT  DECIMAL(10,2),
    STATUT_HABIT     ENUM('en_attente', 'disponible', 'maintenance', 'occupe', 'rejete') DEFAULT 'en_attente',
    PRIMARY KEY (ID_HABIT),
    FOREIGN KEY (ID_PRO)       REFERENCES PROPRIETAIRE(ID_USER),
    FOREIGN KEY (ID_STATION)   REFERENCES STATION(ID_STATION),
    FOREIGN KEY (ID_REG)       REFERENCES REGION(ID_REG)
);

CREATE TABLE PHOTO (
    ID_PHOTO     INT NOT NULL AUTO_INCREMENT,
    ID_HABIT     INT NOT NULL,
    CHEMIN_PHOTO VARCHAR(255) NOT NULL,
    PRIMARY KEY (ID_PHOTO),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE
);

CREATE TABLE APPARTEMENT (
    ID_HABIT      INT NOT NULL,
    ETAGE_APP     INT,
    ASCENSEUR_APP BOOLEAN,
    NUM_APP       VARCHAR(10),
    PRIMARY KEY (ID_HABIT),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE
);

CREATE TABLE MAISON (
    ID_HABIT     INT NOT NULL,
    NB_ETAGES_MAI INT,
    JARDIN_MAI   BOOLEAN,
    GARAGE_MAI   BOOLEAN,
    PRIMARY KEY (ID_HABIT),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE
);

CREATE TABLE CHALET (
    ID_HABIT       INT NOT NULL,
    TYPE_BOIS_CHAL ENUM('Classique', 'Haut de gamme', 'Chalet d''alpage'),
    CHEMINEE_CHA   BOOLEAN,
    PRIMARY KEY (ID_HABIT),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE
);


-- BLOC 3 : SERVICES, ÉQUIPEMENTS ET LIAISONS


CREATE TABLE ACTIVITE (
    ID_ACT       INT NOT NULL AUTO_INCREMENT,
    NOM_ACT      VARCHAR(100) NOT NULL,
    DESCRIPTION_ACT TEXT,
    TYPE_ACT     ENUM('Sport', 'Culture', 'Gastronomie', 'Détente', 'Loisir'),
    PRIX_ACT     DECIMAL(10,2),
    IMAGE_ACT    VARCHAR(255),
    PRIMARY KEY (ID_ACT)
);

CREATE TABLE PROPOSER (
    ID_STATION INT NOT NULL,
    ID_ACT     INT NOT NULL,
    PRIMARY KEY (ID_STATION, ID_ACT),
    FOREIGN KEY (ID_STATION) REFERENCES STATION(ID_STATION) ON DELETE CASCADE,
    FOREIGN KEY (ID_ACT)     REFERENCES ACTIVITE(ID_ACT) ON DELETE CASCADE
);

CREATE TABLE EQUIPEMENT (
    ID_EQUIP       INT NOT NULL AUTO_INCREMENT,
    NOM_EQUIP      VARCHAR(50),
    CATEGORIE_EQUIP VARCHAR(50),
    PRIMARY KEY (ID_EQUIP)
);

CREATE TABLE POSSEDER (
    ID_HABIT  INT NOT NULL,
    ID_EQUIP  INT NOT NULL,
    QUANTITE  INT DEFAULT 1,
    PRIMARY KEY (ID_HABIT, ID_EQUIP),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE,
    FOREIGN KEY (ID_EQUIP) REFERENCES EQUIPEMENT(ID_EQUIP) ON DELETE CASCADE
);


-- BLOC 4 : GESTION COMMERCIALE
-- (RESERVATION doit être créée AVANT RESERVATION_ACTIVITE)


CREATE TABLE CONTRAT (
    ID_CONTRAT          INT NOT NULL AUTO_INCREMENT,
    ID_HABIT            INT NOT NULL,
    DATE_SIGNATURE      DATE,
    DATE_DEBUT          DATE,
    DATE_FIN            DATE,
    STATUT_CONTRAT      ENUM('en_attente', 'valide', 'refuse', 'expire', 'inactif') DEFAULT 'en_attente',
    MOTIF_REFUS_CONTRAT TEXT,
    PRIMARY KEY (ID_CONTRAT),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE
);

CREATE TABLE PERIODE_DISPONIBILITE (
    ID_PERIODE INT NOT NULL AUTO_INCREMENT,
    ID_HABIT   INT NOT NULL,
    DATE_DEBUT DATE NOT NULL,
    DATE_FIN   DATE NOT NULL,
    PRIMARY KEY (ID_PERIODE),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT) ON DELETE CASCADE
);

CREATE TABLE ARCHIVE_CONTRAT (
    ID_ARCHIVE          INT NOT NULL AUTO_INCREMENT,
    ID_CONTRAT_ORIGINE  INT,
    ID_HABIT            INT,
    DATE_SIGNATURE      DATE,
    DATE_DEBUT          DATE,
    DATE_FIN            DATE,
    STATUT_CONTRAT      VARCHAR(20),
    MOTIF_REFUS_CONTRAT TEXT,
    DATE_ARCHIVAGE      DATETIME,
    PRIMARY KEY (ID_ARCHIVE)
);

CREATE TABLE RESERVATION (
    ID_RES         INT NOT NULL AUTO_INCREMENT,
    ID_USER        INT NOT NULL,
    ID_HABIT       INT NOT NULL,
    DATE_RES       DATETIME,
    DATE_DEBUT_RES DATE,
    DATE_FIN_RES   DATE,
    NB_PERSONNES   INT,
    PRIX_TOTAL_RES DECIMAL(10,2),
    STATUT_RES     ENUM('en_attente', 'confirmee', 'annulee'),
    PRIMARY KEY (ID_RES),
    FOREIGN KEY (ID_USER)  REFERENCES CLIENT(ID_USER),
    FOREIGN KEY (ID_HABIT) REFERENCES HABITATION(ID_HABIT)
);

-- RESERVATION_ACTIVITE APRÈS RESERVATION (clé étrangère vers ID_RES)
CREATE TABLE RESERVATION_ACTIVITE (
    ID_RES        INT NOT NULL,
    ID_ACT        INT NOT NULL,
    DATE_ACTIVITE DATE,
    PRIX_ACT      DECIMAL(10,2),
    PRIMARY KEY (ID_RES, ID_ACT),
    FOREIGN KEY (ID_RES) REFERENCES RESERVATION(ID_RES) ON DELETE CASCADE,
    FOREIGN KEY (ID_ACT) REFERENCES ACTIVITE(ID_ACT)    ON DELETE CASCADE
);

CREATE TABLE ARCHIVE_HABITATION (
    ID_ARCHIVE       INT NOT NULL AUTO_INCREMENT,
    ID_HABIT         INT,
    TITRE_HABIT      VARCHAR(100),
    DATE_ARCHIVAGE   DATETIME,
    RAISON_ARCHIVAGE TEXT,
    PRIMARY KEY (ID_ARCHIVE)
);


-- BLOC 5 : TRIGGERS ET PROCÉDURES


DELIMITER //

-- Contrat automatique à l'insertion d'un Appartement
CREATE TRIGGER trg_ins_contrat_app AFTER INSERT ON APPARTEMENT FOR EACH ROW
BEGIN
    DECLARE v_date_debut DATE;
    DECLARE v_date_fin DATE;
    IF MONTH(CURDATE()) >= 10 THEN
        SET v_date_debut = STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-10-01'), '%Y-%m-%d');
        SET v_date_fin   = STR_TO_DATE(CONCAT(YEAR(CURDATE()) + 1, '-09-01'), '%Y-%m-%d');
    ELSE
        SET v_date_debut = STR_TO_DATE(CONCAT(YEAR(CURDATE()) - 1, '-10-01'), '%Y-%m-%d');
        SET v_date_fin   = STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-09-01'), '%Y-%m-%d');
    END IF;
    INSERT INTO CONTRAT (ID_HABIT, DATE_SIGNATURE, DATE_DEBUT, DATE_FIN, STATUT_CONTRAT)
    VALUES (NEW.ID_HABIT, CURDATE(), v_date_debut, v_date_fin, 'en_attente');
END //

-- Contrat automatique à l'insertion d'une Maison
CREATE TRIGGER trg_ins_contrat_mai AFTER INSERT ON MAISON FOR EACH ROW
BEGIN
    DECLARE v_date_debut DATE;
    DECLARE v_date_fin DATE;
    IF MONTH(CURDATE()) >= 10 THEN
        SET v_date_debut = STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-10-01'), '%Y-%m-%d');
        SET v_date_fin   = STR_TO_DATE(CONCAT(YEAR(CURDATE()) + 1, '-09-01'), '%Y-%m-%d');
    ELSE
        SET v_date_debut = STR_TO_DATE(CONCAT(YEAR(CURDATE()) - 1, '-10-01'), '%Y-%m-%d');
        SET v_date_fin   = STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-09-01'), '%Y-%m-%d');
    END IF;
    INSERT INTO CONTRAT (ID_HABIT, DATE_SIGNATURE, DATE_DEBUT, DATE_FIN, STATUT_CONTRAT)
    VALUES (NEW.ID_HABIT, CURDATE(), v_date_debut, v_date_fin, 'en_attente');
END //

-- Contrat automatique à l'insertion d'un Chalet
CREATE TRIGGER trg_ins_contrat_cha AFTER INSERT ON CHALET FOR EACH ROW
BEGIN
    DECLARE v_date_debut DATE;
    DECLARE v_date_fin DATE;
    IF MONTH(CURDATE()) >= 10 THEN
        SET v_date_debut = STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-10-01'), '%Y-%m-%d');
        SET v_date_fin   = STR_TO_DATE(CONCAT(YEAR(CURDATE()) + 1, '-09-01'), '%Y-%m-%d');
    ELSE
        SET v_date_debut = STR_TO_DATE(CONCAT(YEAR(CURDATE()) - 1, '-10-01'), '%Y-%m-%d');
        SET v_date_fin   = STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-09-01'), '%Y-%m-%d');
    END IF;
    INSERT INTO CONTRAT (ID_HABIT, DATE_SIGNATURE, DATE_DEBUT, DATE_FIN, STATUT_CONTRAT)
    VALUES (NEW.ID_HABIT, CURDATE(), v_date_debut, v_date_fin, 'en_attente');
END //

-- Anti-chevauchement des réservations
CREATE TRIGGER trg_verif_dispo_reservation BEFORE INSERT ON RESERVATION FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM RESERVATION
        WHERE ID_HABIT = NEW.ID_HABIT
        AND STATUT_RES = 'confirmee'
        AND (NEW.DATE_DEBUT_RES < DATE_FIN_RES AND NEW.DATE_FIN_RES > DATE_DEBUT_RES)
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ce logement est déjà réservé pour ces dates.';
    END IF;
END //

-- Date d'inscription auto pour les propriétaires
DROP TRIGGER IF EXISTS trg_insert_proprietaire //
CREATE TRIGGER trg_insert_proprietaire BEFORE INSERT ON PROPRIETAIRE FOR EACH ROW
BEGIN
    IF NEW.DATE_INSCRIPTION IS NULL THEN SET NEW.DATE_INSCRIPTION = CURDATE(); END IF;
END //

-- Niveau d'accès auto pour les admins
DROP TRIGGER IF EXISTS trg_insert_admin //
CREATE TRIGGER trg_insert_admin BEFORE INSERT ON ADMINISTRATEUR FOR EACH ROW
BEGIN
    IF NEW.NIVEAU_ACCES IS NULL THEN SET NEW.NIVEAU_ACCES = 1; END IF;
END //

-- Archivage automatique avant suppression d'une habitation
DROP TRIGGER IF EXISTS trg_archive_habitation_delete //
CREATE TRIGGER trg_archive_habitation_delete BEFORE DELETE ON HABITATION FOR EACH ROW
BEGIN
    INSERT INTO ARCHIVE_HABITATION (ID_HABIT, TITRE_HABIT, DATE_ARCHIVAGE, RAISON_ARCHIVAGE)
    VALUES (OLD.ID_HABIT, OLD.TITRE_HABIT, NOW(), 'Suppression manuelle ou fin de contrat');
END //

-- Passage en 'disponible' quand le contrat est validé
CREATE TRIGGER trg_update_statut_apres_contrat AFTER UPDATE ON CONTRAT FOR EACH ROW
BEGIN
    IF NEW.STATUT_CONTRAT = 'valide' THEN
        UPDATE HABITATION SET STATUT_HABIT = 'disponible' WHERE ID_HABIT = NEW.ID_HABIT;
    ELSEIF NEW.STATUT_CONTRAT = 'refuse' THEN
        UPDATE HABITATION SET STATUT_HABIT = 'rejete' WHERE ID_HABIT = NEW.ID_HABIT;
    ELSEIF NEW.STATUT_CONTRAT = 'inactif' THEN
        UPDATE HABITATION SET STATUT_HABIT = 'maintenance' WHERE ID_HABIT = NEW.ID_HABIT;
    END IF;
END //

 
CREATE TABLE SAISON (
    ID_SAISON      INT NOT NULL AUTO_INCREMENT,
    NOM_SAISON     ENUM('basse', 'haute') NOT NULL,
    MOIS_DEBUT     INT NOT NULL,
    MOIS_FIN       INT NOT NULL,
    COEFFICIENT    DECIMAL(4,2) NOT NULL,
    PRIMARY KEY (ID_SAISON)
);
 
INSERT INTO SAISON (NOM_SAISON, MOIS_DEBUT, MOIS_FIN, COEFFICIENT) VALUES
('basse', 9, 3,  0.90),
('haute', 4, 8,  1.10);
 


DELIMITER //
 
DROP TRIGGER IF EXISTS trg_auto_prix_reservation //
 
CREATE TRIGGER trg_auto_prix_reservation BEFORE INSERT ON RESERVATION
FOR EACH ROW
BEGIN
    DECLARE v_prix_nuit   DECIMAL(10,2);
    DECLARE v_nb_jours    INT;
    DECLARE v_coefficient DECIMAL(4,2);
    DECLARE v_mois        INT;
 

    SELECT PRIX_NUIT_HABIT INTO v_prix_nuit
    FROM HABITATION
    WHERE ID_HABIT = NEW.ID_HABIT;
 

    SET v_nb_jours = DATEDIFF(NEW.DATE_FIN_RES, NEW.DATE_DEBUT_RES);

    SET v_mois = MONTH(NEW.DATE_DEBUT_RES);
 
    IF v_mois >= 4 AND v_mois <= 8 THEN
        SET v_coefficient = 1.10;
    ELSE
        SET v_coefficient = 0.90;
    END IF;
 
    SET NEW.PRIX_TOTAL_RES = v_prix_nuit * NEW.NB_PERSONNES * v_nb_jours * v_coefficient;
END //
 
DELIMITER ;

-- Procédure de recalcul manuel du prix
DELIMITER //
 
DROP PROCEDURE IF EXISTS sp_calculer_prix_reservation //
 
CREATE PROCEDURE sp_calculer_prix_reservation(IN p_id_res INT)
BEGIN
    DECLARE v_mois        INT;
    DECLARE v_coefficient DECIMAL(4,2);
 
    SELECT MONTH(r.DATE_DEBUT_RES) INTO v_mois
    FROM RESERVATION r WHERE r.ID_RES = p_id_res;
 
    IF v_mois >= 4 AND v_mois <= 8 THEN
        SET v_coefficient = 1.10;
    ELSE
        SET v_coefficient = 0.90;
    END IF;
 
    UPDATE RESERVATION r
    JOIN HABITATION h ON r.ID_HABIT = h.ID_HABIT
    SET r.PRIX_TOTAL_RES = h.PRIX_NUIT_HABIT * r.NB_PERSONNES
                         * DATEDIFF(r.DATE_FIN_RES, r.DATE_DEBUT_RES)
                         * v_coefficient
    WHERE r.ID_RES = p_id_res;
END //
 
DELIMITER ;

INSERT INTO RESERVATION (ID_USER, ID_HABIT, DATE_RES, DATE_DEBUT_RES, DATE_FIN_RES, NB_PERSONNES, STATUT_RES)
VALUES (4, 6, NOW(), '2026-07-01', '2026-07-08', 3, 'confirmee');

SELECT PRIX_TOTAL_RES FROM RESERVATION ORDER BY ID_RES DESC LIMIT 1;

/* Normalement 260 × 3 × 7 × 1.10 = 6006 € */


-- BLOC 6 : VUES


CREATE VIEW V_UTILISATEURS AS
SELECT U.*, P.RIB_PRO, A.POSTE_ADMIN, C.DATE_NAISSANCE_CLI
FROM USER U
LEFT JOIN PROPRIETAIRE  P ON U.ID_USER = P.ID_USER
LEFT JOIN ADMINISTRATEUR A ON U.ID_USER = A.ID_USER
LEFT JOIN CLIENT         C ON U.ID_USER = C.ID_USER;

CREATE VIEW V_CATALOGUE_LOGEMENTS AS
SELECT H.*, S.NOM_STATION, R.NOM_REG
FROM HABITATION H
JOIN STATION S  ON H.ID_STATION = S.ID_STATION
JOIN REGION R   ON H.ID_REG     = R.ID_REG
JOIN CONTRAT C  ON H.ID_HABIT   = C.ID_HABIT
WHERE H.STATUT_HABIT = 'disponible' AND C.STATUT_CONTRAT = 'valide';

CREATE VIEW V_SYNTHESE_BIENS AS
SELECT
    H.ID_HABIT,
    H.TITRE_HABIT,
    H.PRIX_NUIT_HABIT,
    S.NOM_STATION,
    CASE
        WHEN A.ID_HABIT  IS NOT NULL THEN 'Appartement'
        WHEN M.ID_HABIT  IS NOT NULL THEN 'Maison'
        WHEN CH.ID_HABIT IS NOT NULL THEN 'Chalet'
        ELSE 'Inconnu'
    END AS TYPE_LOGEMENT
FROM HABITATION H
JOIN STATION  S  ON H.ID_STATION = S.ID_STATION
LEFT JOIN APPARTEMENT A  ON H.ID_HABIT = A.ID_HABIT
LEFT JOIN MAISON      M  ON H.ID_HABIT = M.ID_HABIT
LEFT JOIN CHALET      CH ON H.ID_HABIT = CH.ID_HABIT;

CREATE VIEW V_CATALOGUE_BIENS AS
SELECT
    H.*,
    S.NOM_STATION,
    CASE
        WHEN A.ID_HABIT  IS NOT NULL THEN 'Appartement'
        WHEN M.ID_HABIT  IS NOT NULL THEN 'Maison'
        WHEN CH.ID_HABIT IS NOT NULL THEN 'Chalet'
    END AS TYPE_LOGEMENT
FROM HABITATION H
LEFT JOIN STATION     S  ON H.ID_STATION = S.ID_STATION
LEFT JOIN APPARTEMENT A  ON H.ID_HABIT = A.ID_HABIT
LEFT JOIN MAISON      M  ON H.ID_HABIT = M.ID_HABIT
LEFT JOIN CHALET      CH ON H.ID_HABIT = CH.ID_HABIT;


-- BLOC 7 : JEU DE DONNÉES


-- Régions
INSERT INTO REGION (NOM_REG, DEP_REG) VALUES
('Alpes du Nord', '74'),
('Alpes du Sud',  '05'),
('Pyrénées',      '65');

-- Stations
INSERT INTO STATION (ID_REG, NOM_STATION, NB_HABITANTS) VALUES
(1, 'Chamonix',    8900),
(2, 'Vars',         600),
(1, 'Val d''Isère', 1600);

-- Équipements
INSERT INTO EQUIPEMENT (NOM_EQUIP, CATEGORIE_EQUIP) VALUES
('Lave-vaisselle',      'Cuisine'),
('Wifi Fibre',          'Multimédia'),
('Sauna privé',         'Luxe'),
('Appareil à raclette', 'Cuisine'),
('Garage',              'Pratique'),
('Balcon',              'Extérieur');

-- Activités
INSERT INTO ACTIVITE (NOM_ACT, TYPE_ACT, PRIX_ACT, DESCRIPTION_ACT) VALUES
('Ski Alpin',          'Sport',   45.00, 'Descentes sur les pistes balisées du domaine skiable.'),
('Chiens de traîneau', 'Sport',   60.00, 'Balade en forêt avec une équipe de chiens de traîneau.'),
('Spa Thermal',        'Détente', 30.00, 'Accès illimité aux bains thermaux et hammam.');

-- Liaisons Station <-> Activité
INSERT INTO PROPOSER (ID_STATION, ID_ACT) VALUES
(1, 1), (1, 3),
(2, 1), (2, 2);

-- Utilisateurs (table mère)
INSERT INTO USER (NOM_USER, PRENOM_USER, EMAIL_USER, MDP_USER, ROLE_USER, CP_USER, VILLE_USER) VALUES
('Boss',     'Directeur', 'admin@neige.fr',    'admin123', 'admin',        '05000', 'Gap'),
('Lefebvre', 'Thomas',    'thomas@test.fr',    '123',      'proprietaire', '74000', 'Annecy'),
('Dubois',   'Marie',     'marie@test.fr',     '123',      'proprietaire', '69000', 'Lyon'),
('Gauthier', 'Julien',    'julien@client.fr',  '123',      'client',       '75000', 'Paris');

-- Tables filles utilisateurs
INSERT INTO ADMINISTRATEUR (ID_USER, POSTE_ADMIN, NIVEAU_ACCES) VALUES (1, 'Gérant', 2);
INSERT INTO PROPRIETAIRE   (ID_USER, RIB_PRO, DATE_INSCRIPTION) VALUES
(2, 'FR7612345', CURDATE()),
(3, 'FR7698765', CURDATE());
INSERT INTO CLIENT (ID_USER, DATE_NAISSANCE_CLI) VALUES (4, '1990-05-15');


-- HABITATIONS
-- Note : les triggers créent automatiquement les contrats
-- à l'insertion dans APPARTEMENT / MAISON / CHALET
--
-- IDs après insertion :
--   Appartements : 1=Studio Cristal, 2=T2 Mont-Blanc,
--                  3=Le Panoramic,   4=Appart Chamois
--   Maisons      : 5=Chalet Bellevue, 6=Maison de Bois
--   Chalets      : 7=Chalet Sapin,    8=Chalet Royal


-- Appartements (IMAGE_HABIT = NULL volontairement → motif de refus)
INSERT INTO HABITATION (ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, DESCRIPTION_HABIT,
    PRIX_NUIT_HABIT, VILLE_HABIT, CP_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
    NB_LITS_HABIT, NB_GUETS_HABIT, STATUT_HABIT, IMAGE_HABIT) VALUES
(2, 1, 1, 'Studio Cristal',  'Studio cosy au cœur de Chamonix, idéal pour 2 personnes.',
    65,  'Chamonix', '74400', 25.0, 1, 1, 2, 'en_attente', NULL),
(2, 1, 1, 'T2 Mont-Blanc',   'Appartement lumineux avec vue sur le Mont-Blanc.',
    95,  'Chamonix', '74400', 42.0, 1, 2, 4, 'en_attente', NULL),
(3, 2, 2, 'Le Panoramic',    'Grand appartement panoramique face aux pistes de Vars.',
    150, 'Vars',     '05560', 65.0, 2, 3, 6, 'en_attente', NULL),
(3, 3, 1, 'Appart Chamois',  'Appartement calme à deux pas des remontées mécaniques de Val d''Isère.',
    85,  'Val d''Isère', '73150', 38.0, 1, 2, 4, 'en_attente', NULL);

INSERT INTO APPARTEMENT (ID_HABIT, ETAGE_APP, ASCENSEUR_APP) VALUES
(1, 2, FALSE),
(2, 4, TRUE),
(3, 1, FALSE),
(4, 3, TRUE);

-- Maisons (avec images)
INSERT INTO HABITATION (ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, DESCRIPTION_HABIT,
    PRIX_NUIT_HABIT, VILLE_HABIT, CP_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
    NB_LITS_HABIT, NB_GUETS_HABIT, STATUT_HABIT, IMAGE_HABIT) VALUES
(2, 1, 1, 'Chalet Bellevue', 'Belle maison en bois avec jardin et terrasse ensoleillée à Chamonix.',
    210, 'Chamonix', '74400', 110.0, 3, 5, 8,  'en_attente', 'assets/img/locations/maison1.jpg'),
(3, 1, 1, 'Maison de Bois',  'Maison traditionnelle savoyarde avec garage et jardin privatif.',
    260, 'Chamonix', '74400', 140.0, 5, 8, 12, 'en_attente', 'assets/img/locations/maison2.jpg');

INSERT INTO MAISON (ID_HABIT, NB_ETAGES_MAI, JARDIN_MAI, GARAGE_MAI) VALUES
(5, 2, TRUE,  TRUE),
(6, 2, TRUE,  FALSE);

-- Chalets (avec images)
INSERT INTO HABITATION (ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, DESCRIPTION_HABIT,
    PRIX_NUIT_HABIT, VILLE_HABIT, CP_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
    NB_LITS_HABIT, NB_GUETS_HABIT, STATUT_HABIT, IMAGE_HABIT) VALUES
(2, 1, 1, 'Chalet Sapin',  'Chalet authentique avec cheminée, sauna et vue imprenable sur les Alpes.',
    320, 'Chamonix',     '74400', 180.0, 5, 8, 12, 'en_attente', 'assets/img/locations/chalet1.jpg'),
(3, 3, 1, 'Chalet Royal',  'Chalet haut de gamme avec spa privatif et cuisine gastronomique équipée.',
    650, 'Val d''Isère', '73150', 300.0, 8, 14, 20, 'en_attente', 'assets/img/locations/chalet2.jpg');

INSERT INTO CHALET (ID_HABIT, TYPE_BOIS_CHAL, CHEMINEE_CHA) VALUES
(7, 'Classique',      TRUE),
(8, 'Haut de gamme',  TRUE);


-- ÉQUIPEMENTS PAR HABITATION

INSERT INTO POSSEDER (ID_HABIT, ID_EQUIP, QUANTITE) VALUES
(1, 2, 1),  -- Studio Cristal     : Wifi
(2, 2, 1),  -- T2 Mont-Blanc      : Wifi
(2, 6, 1),  -- T2 Mont-Blanc      : Balcon
(3, 1, 1),  -- Le Panoramic       : Lave-vaisselle
(3, 2, 1),  -- Le Panoramic       : Wifi
(4, 2, 1),  -- Appart Chamois     : Wifi
(5, 1, 1),  -- Chalet Bellevue    : Lave-vaisselle
(5, 5, 1),  -- Chalet Bellevue    : Garage
(5, 6, 1),  -- Chalet Bellevue    : Balcon
(6, 1, 1),  -- Maison de Bois     : Lave-vaisselle
(6, 5, 1),  -- Maison de Bois     : Garage
(7, 2, 1),  -- Chalet Sapin       : Wifi
(7, 3, 1),  -- Chalet Sapin       : Sauna
(7, 4, 1),  -- Chalet Sapin       : Raclette
(8, 2, 1),  -- Chalet Royal       : Wifi
(8, 3, 1),  -- Chalet Royal       : Sauna
(8, 4, 1),  -- Chalet Royal       : Raclette
(8, 6, 1);  -- Chalet Royal       : Balcon


-- SCÉNARIO DE TEST


-- 1. Refus des 4 appartements (sans photo) — les contrats existent déjà via trigger
UPDATE HABITATION SET STATUT_HABIT = 'rejete' WHERE ID_HABIT IN (1, 2, 3, 4);
UPDATE CONTRAT SET STATUT_CONTRAT = 'refuse',
    MOTIF_REFUS_CONTRAT = 'Demande rejetée : aucune photographie. Un minimum de 3 photos est requis pour valider l''annonce.'
WHERE ID_HABIT = 1 AND STATUT_CONTRAT = 'en_attente';
UPDATE CONTRAT SET STATUT_CONTRAT = 'refuse',
    MOTIF_REFUS_CONTRAT = 'Dossier incomplet : l''absence de visuels ne permet pas d''évaluer la conformité du bien. Uploadez des photos dans votre espace propriétaire.'
WHERE ID_HABIT = 2 AND STATUT_CONTRAT = 'en_attente';
UPDATE CONTRAT SET STATUT_CONTRAT = 'refuse',
    MOTIF_REFUS_CONTRAT = 'Annonce refusée : aucune photo du logement. Veuillez uploader au minimum 3 photos (séjour, chambre, cuisine) pour soumettre à nouveau.'
WHERE ID_HABIT = 3 AND STATUT_CONTRAT = 'en_attente';
UPDATE CONTRAT SET STATUT_CONTRAT = 'refuse',
    MOTIF_REFUS_CONTRAT = 'Dossier incomplet : absence de visuels. Les photos sont obligatoires pour valider une annonce sur notre plateforme.'
WHERE ID_HABIT = 4 AND STATUT_CONTRAT = 'en_attente';

-- 2. Validation des maisons (ID 5 et 6) — disponibles pour les clients
UPDATE CONTRAT SET STATUT_CONTRAT = 'valide' WHERE ID_HABIT IN (5, 6) AND STATUT_CONTRAT = 'en_attente';
-- Le trigger trg_update_statut_apres_contrat passe automatiquement STATUT_HABIT à 'disponible'

-- 3. Galerie photos (IDs corrects : maisons=5,6 / chalets=7,8)
INSERT INTO PHOTO (ID_HABIT, CHEMIN_PHOTO) VALUES
(5, 'assets/img/locations/gallery/maison1_1.jpg'),
(5, 'assets/img/locations/gallery/maison1_2.jpg'),
(5, 'assets/img/locations/gallery/maison1_3.jpg'),
(5, 'assets/img/locations/gallery/maison1_4.jpg'),
(6, 'assets/img/locations/gallery/maison2_1.jpg'),
(6, 'assets/img/locations/gallery/maison2_2.jpg'),
(7, 'assets/img/locations/gallery/chalet1_1.jpg'),
(7, 'assets/img/locations/gallery/chalet1_2.jpg'),
(7, 'assets/img/locations/gallery/chalet1_3.jpg'),
(8, 'assets/img/locations/gallery/chalet2_1.jpg'),
(8, 'assets/img/locations/gallery/chalet2_2.jpg');

-- 4. Réservation de test (client Julien sur la Maison de Bois)
INSERT INTO RESERVATION (ID_USER, ID_HABIT, DATE_RES, DATE_DEBUT_RES, DATE_FIN_RES, NB_PERSONNES, STATUT_RES) VALUES
(4, 6, NOW(), '2026-04-05', '2026-04-12', 4, 'confirmee');


-- VÉRIFICATION FINALE

SELECT H.ID_HABIT, H.TITRE_HABIT, H.STATUT_HABIT, C.STATUT_CONTRAT,
       CASE WHEN H.IMAGE_HABIT IS NULL THEN '⚠ Pas d image' ELSE '✓ Image OK' END AS IMAGE
FROM HABITATION H
LEFT JOIN CONTRAT C ON H.ID_HABIT = C.ID_HABIT
ORDER BY H.ID_HABIT;


-- NOUVELLES HABITATIONS
-- Statut : disponible
-- Galerie : 1 photo extérieure principale + 1-2 intérieures
-- Répartition : Chamonix (1), Vars (2), Val d'Isère (3)


-- ──────────────────────────────────────────────────────────────────
-- APPARTEMENTS
-- ID 9  : Vars        — façade résidence village + cuisine moderne
-- ID 10 : Val d'Isère — façade flanc montagne    + grand séjour
-- ──────────────────────────────────────────────────────────────────

INSERT INTO HABITATION (ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, DESCRIPTION_HABIT,
    PRIX_NUIT_HABIT, VILLE_HABIT, CP_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
    NB_LITS_HABIT, NB_GUETS_HABIT, STATUT_HABIT, IMAGE_HABIT) VALUES
(3, 2, 2,
 'L''Éclaircie — Vars',
 'Appartement lumineux dans une résidence au cœur des Alpes du Sud, à deux pas des pistes de Vars. Cuisine entièrement rénovée avec poutres apparentes, balcon exposé sud et vue sur le massif. Idéal pour un séjour hivernal en famille.',
 110, 'Vars', '05560', 45.0, 2, 2, 4, 'disponible',
 'assets/img/locations/residence_village.jpg'),

(2, 3, 1,
 'Cosy Isère — Val d''Isère',
 'Appartement de caractère dans un village alpin typique en flanc de montagne, à Val d''Isère. Séjour ouvert baigné de lumière avec grand canapé d''angle, accès direct aux remontées mécaniques et cuisine entièrement équipée.',
 135, 'Val d''Isère', '73150', 52.0, 2, 3, 5, 'disponible',
 'assets/img/locations/maison_flanc_montagne.jpg');

INSERT INTO APPARTEMENT (ID_HABIT, ETAGE_APP, ASCENSEUR_APP) VALUES
(9,  2, FALSE),
(10, 3, TRUE);

-- Galerie appartements
INSERT INTO PHOTO (ID_HABIT, CHEMIN_PHOTO) VALUES
(9,  'assets/img/locations/gallery/int_cuisine_moderne.jpg'),
(10, 'assets/img/locations/gallery/int_grand_sejour.jpg');

-- ──────────────────────────────────────────────────────────────────
-- MAISONS
-- ID 11 : Chamonix    — maison alpine jardin vert  + chambre vichy
-- ID 12 : Vars        — maison pierre enneigée     + séjour cheminée brique
-- ID 13 : Val d'Isère — maison village flanc       + cuisine design sombre
-- ──────────────────────────────────────────────────────────────────

INSERT INTO HABITATION (ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, DESCRIPTION_HABIT,
    PRIX_NUIT_HABIT, VILLE_HABIT, CP_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
    NB_LITS_HABIT, NB_GUETS_HABIT, STATUT_HABIT, IMAGE_HABIT) VALUES
(2, 1, 1,
 'Chalet Berger — Chamonix',
 'Grande maison alpine savoyarde avec jardins verdoyants face aux sommets de Chamonix. Intérieur spacieux aux chambres décorées dans un style montagne authentique, parquet massif, poutres et volets en bois ciré.',
 230, 'Chamonix', '74400', 125.0, 4, 6, 8, 'disponible',
 'assets/img/locations/maison_alpine_verte.jpg'),

(3, 2, 2,
 'La Forge — Vars',
 'Maison en pierre et bois au cœur du village de Vars, ensevelie sous la neige en hiver. Séjour chaleureux avec cheminée en brique, cuisine américaine ouverte et terrasse couverte pour profiter des soirées sous les étoiles.',
 195, 'Vars', '05560', 110.0, 3, 5, 7, 'disponible',
 'assets/img/locations/maison_pierre_enneigee.jpg'),

(2, 3, 1,
 'Maison Hameau — Val d''Isère',
 'Authentique maison de village au milieu d''un hameau traditionnel de Val d''Isère, entourée de chalets centenaires. Cuisine et salle à manger dans un esprit chalet design avec luminaires industriels et mobilier sur mesure.',
 265, 'Val d''Isère', '73150', 140.0, 4, 7, 10, 'disponible',
 'assets/img/locations/chalets_anciens.jpg');

INSERT INTO MAISON (ID_HABIT, NB_ETAGES_MAI, JARDIN_MAI, GARAGE_MAI) VALUES
(11, 2, TRUE,  FALSE),
(12, 1, TRUE,  TRUE),
(13, 2, FALSE, TRUE);

-- Galerie maisons
INSERT INTO PHOTO (ID_HABIT, CHEMIN_PHOTO) VALUES
(11, 'assets/img/locations/gallery/int_chambre_alpine.jpg'),
(12, 'assets/img/locations/gallery/int_sejour_cheminee.jpg'),
(13, 'assets/img/locations/gallery/int_cuisine_design.jpg');

-- ──────────────────────────────────────────────────────────────────
-- CHALETS
-- ID 14 : Chamonix    — chalet fleuri été          + séjour rustique bois + fondue
-- ID 15 : Vars        — chalet sauna sous neige    + séjour A-frame design
-- ID 16 : Val d'Isère — chalet neige/Cervin        + poêle pin cosy + chambre
-- ID 17 : Chamonix    — grand chalet pierre/bois   + grand séjour spacieux
-- ──────────────────────────────────────────────────────────────────

INSERT INTO HABITATION (ID_PRO, ID_STATION, ID_REG, TITRE_HABIT, DESCRIPTION_HABIT,
    PRIX_NUIT_HABIT, VILLE_HABIT, CP_HABIT, SURFACE_HABIT, NB_CHAMBRES_HABIT,
    NB_LITS_HABIT, NB_GUETS_HABIT, STATUT_HABIT, IMAGE_HABIT) VALUES
(2, 1, 1,
 'Chalet Fleurs d''Alpes — Chamonix',
 'Chalet savoyarde fleuri avec terrasse et balcon donnant sur les Alpes de Chamonix. Magnifique séjour ouvert avec cuisine équipée en bois massif, cheminée en pierre et ambiance raclette garantie. Sauna privatif en option.',
 285, 'Chamonix', '74400', 160.0, 4, 7, 10, 'disponible',
 'assets/img/locations/maison_fleurie.jpg'),

(3, 2, 2,
 'Chalet Tonneau — Vars',
 'Chalet contemporain à Vars avec sauna en tonneau dans le jardin enneigé. Intérieur épuré de style A-frame avec grandes verrières, mezzanine suspendue et canapés en velours. Parfait pour un séjour luxe au ski.',
 420, 'Vars', '05560', 195.0, 5, 8, 12, 'disponible',
 'assets/img/locations/chalet_sauna_neige.jpg'),

(2, 3, 1,
 'Chalet Cervin — Val d''Isère',
 'Chalet d''exception face aux plus hauts sommets de Val d''Isère, toit chargé de neige et fenêtres illuminées au coucher de soleil. Intérieur cosy en pin massif avec poêle à bois, fourrures au sol et chambre vue montagne.',
 380, 'Val d''Isère', '73150', 175.0, 5, 8, 12, 'disponible',
 'assets/img/locations/chalet_cervin.jpg'),

(3, 1, 1,
 'Grand Chalet Alpin — Chamonix',
 'Imposant chalet en pierre et bois massif face aux sommets de Chamonix, avec garage intégré pour le matériel de ski. Vaste séjour lumineux double hauteur avec baies vitrées, parquet chêne et accès direct à la terrasse panoramique.',
 580, 'Chamonix', '74400', 280.0, 6, 11, 16, 'disponible',
 'assets/img/locations/chalet_pierre_bois.jpg');

INSERT INTO CHALET (ID_HABIT, TYPE_BOIS_CHAL, CHEMINEE_CHA) VALUES
(14, 'Savoyard',      TRUE),
(15, 'A-Frame',       FALSE),
(16, 'Pin massif',    TRUE),
(17, 'Pierre et Bois',TRUE);

-- Galerie chalets
INSERT INTO PHOTO (ID_HABIT, CHEMIN_PHOTO) VALUES
(14, 'assets/img/locations/gallery/int_sejour_rustique.jpg'),
(14, 'assets/img/locations/gallery/int_fondue_vin.jpg'),
(15, 'assets/img/locations/gallery/int_sejour_aframe.jpg'),
(16, 'assets/img/locations/gallery/int_poele_pin.jpg'),
(16, 'assets/img/locations/gallery/int_chambre_cosy.jpg'),
(17, 'assets/img/locations/gallery/int_grand_sejour.jpg');

-- ──────────────────────────────────────────────────────────────────
-- ÉQUIPEMENTS
-- ──────────────────────────────────────────────────────────────────

INSERT INTO POSSEDER (ID_HABIT, ID_EQUIP, QUANTITE) VALUES
-- L'Éclaircie (9) : Wifi + Balcon
(9,  2, 1), (9,  6, 1),
-- Cosy Isère (10) : Wifi + Balcon
(10, 2, 1), (10, 6, 1),
-- Chalet Berger (11) : Wifi + Lave-vaisselle + Balcon
(11, 2, 1), (11, 1, 1), (11, 6, 1),
-- La Forge (12) : Wifi + Lave-vaisselle + Garage
(12, 2, 1), (12, 1, 1), (12, 5, 1),
-- Maison Hameau (13) : Wifi + Lave-vaisselle + Garage
(13, 2, 1), (13, 1, 1), (13, 5, 1),
-- Chalet Fleurs d'Alpes (14) : Wifi + Raclette + Balcon
(14, 2, 1), (14, 4, 1), (14, 6, 1),
-- Chalet Tonneau (15) : Wifi + Sauna + Raclette
(15, 2, 1), (15, 3, 1), (15, 4, 1),
-- Chalet Cervin (16) : Wifi + Sauna + Raclette
(16, 2, 1), (16, 3, 1), (16, 4, 1),
-- Grand Chalet Alpin (17) : Wifi + Lave-vaisselle + Sauna + Garage
(17, 2, 1), (17, 1, 1), (17, 3, 1), (17, 5, 1);

