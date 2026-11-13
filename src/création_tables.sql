-- 1. TABLE UTILISATEUR
CREATE TABLE UTILISATEUR (
    id_utilisateur INT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(100),
    nb_touites INT DEFAULT 0,

    CONSTRAINT pk_utilisateur PRIMARY KEY (id_utilisateur),
    CONSTRAINT uq_utilisateur_email UNIQUE (email)
) ENGINE=InnoDB;


-- 2. TABLE TOUITE
CREATE TABLE TOUITE (
    id_touite INT,
    contenu VARCHAR(235),
    date_publication DATETIME,
    id_utilisateur INT,

    CONSTRAINT pk_touite PRIMARY KEY (id_touite),

    CONSTRAINT fk_touite_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES UTILISATEUR(id_utilisateur)
) ENGINE=InnoDB;


-- 3. TABLE TAG
CREATE TABLE TAG (
    id_tag INT,
    libelle VARCHAR(15),
    description VARCHAR(50),

    CONSTRAINT pk_tag PRIMARY KEY (id_tag),
    CONSTRAINT uq_tag_libelle UNIQUE (libelle)
) ENGINE=InnoDB;


-- 4. TABLE IMAGE
CREATE TABLE IMAGE (
    id_image INT,
    description VARCHAR(50),
    chemin_fichier VARCHAR(500),
    id_touite INT,

    CONSTRAINT pk_image PRIMARY KEY (id_image),

    CONSTRAINT fk_image_touite
        FOREIGN KEY (id_touite)
        REFERENCES TOUITE(id_touite)
) ENGINE=InnoDB;


-- 5. TABLE SUIVRE
CREATE TABLE SUIVRE (
    id_utilisateur_abonne INT,
    id_utilisateur_suivi INT,

    CONSTRAINT pk_suivre PRIMARY KEY (
        id_utilisateur_abonne,
        id_utilisateur_suivi
    ),
    CONSTRAINT fk_suivre_abonne
        FOREIGN KEY (id_utilisateur_abonne)
        REFERENCES UTILISATEUR(id_utilisateur),
    CONSTRAINT fk_suivre_suivi
        FOREIGN KEY (id_utilisateur_suivi)
        REFERENCES UTILISATEUR(id_utilisateur)
);


-- 6. TABLE ABONNEMENT_TAG
CREATE TABLE ABONNEMENT_TAG (
    id_utilisateur INT,
    id_tag INT,

    CONSTRAINT pk_abonnement_tag
        PRIMARY KEY (id_utilisateur, id_tag),

    CONSTRAINT fk_abonnement_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES UTILISATEUR(id_utilisateur),

    CONSTRAINT fk_abonnement_tag
        FOREIGN KEY (id_tag)
        REFERENCES TAG(id_tag)
) ENGINE=InnoDB;


-- 7. TABLE TOUITE_TAG
CREATE TABLE TOUITE_TAG (
    id_touite INT,
    id_tag INT,

    CONSTRAINT pk_touite_tag
        PRIMARY KEY (id_touite, id_tag),

    CONSTRAINT fk_touite_tag_touite
        FOREIGN KEY (id_touite)
        REFERENCES TOUITE(id_touite),

    CONSTRAINT fk_touite_tag_tag
        FOREIGN KEY (id_tag)
        REFERENCES TAG(id_tag)
) ENGINE=InnoDB;


-- 8. TABLE COMMENTAIRE
CREATE TABLE COMMENTAIRE (
    id_commentaire INT,
    contenu VARCHAR(235),
    date_commentaire DATETIME,
    id_utilisateur INT,
    id_touite INT,

    CONSTRAINT pk_commentaire
        PRIMARY KEY (id_commentaire),

    CONSTRAINT fk_commentaire_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES UTILISATEUR(id_utilisateur),

    CONSTRAINT fk_commentaire_touite
        FOREIGN KEY (id_touite)
        REFERENCES TOUITE(id_touite)
) ENGINE=InnoDB;


--9. TABLE VOTE
CREATE TABLE IF NOT EXISTS VOTE (
    id_utilisateur INT NOT NULL,
    id_touite INT NOT NULL,
    valeur SMALLINT NOT NULL,
    PRIMARY KEY (id_utilisateur, id_touite),
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
    FOREIGN KEY (id_touite) REFERENCES TOUITE(id_touite),
    CHECK (valeur IN (-1, 1))
) ENGINE=InnoDB;
