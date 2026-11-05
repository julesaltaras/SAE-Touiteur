
CREATE TABLE IF NOT EXISTS VOTE (
    id_utilisateur INT NOT NULL,
    id_touite INT NOT NULL,
    valeur SMALLINT NOT NULL,
    PRIMARY KEY (id_utilisateur, id_touite),
    FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur),
    FOREIGN KEY (id_touite) REFERENCES TOUITE(id_touite),
    CHECK (valeur IN (-1, 1))
) ENGINE=InnoDB;


SET NAMES utf8mb4;
START TRANSACTION;
INSERT INTO UTILISATEUR(id_utilisateur,nom,prenom,email) VALUES
 (1,'Martin','Alice','alice@example.test'),(2,'Bernard','Lucas','lucas@example.test'),
 (3,'Petit','Emma','emma@example.test'),(4,'Robert','Noah','noah@example.test');
INSERT INTO TAG(id_tag,libelle,description) VALUES
 (1,'informatique','Du code et des idées'),(2,'nancy','La vie à Nancy'),(3,'photo','Le regard des photographes'),(4,'jeux','Jeux et découvertes');
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (1,'Bienvenue sur Touiteur ! Un projet #informatique à construire ensemble.',CURRENT_TIMESTAMP - INTERVAL 16 HOUR,1);
INSERT INTO TOUITE_TAG VALUES (1,1);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (2,'Une promenade au bord de la Meurthe. #nancy #photo',CURRENT_TIMESTAMP - INTERVAL 15 HOUR,2);
INSERT INTO TOUITE_TAG VALUES (2,2);
INSERT INTO TOUITE_TAG VALUES (2,3);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (3,'Un jeu indépendant à découvrir ce week-end. #jeux',CURRENT_TIMESTAMP - INTERVAL 14 HOUR,3);
INSERT INTO TOUITE_TAG VALUES (3,4);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (4,'Les requêtes préparées protègent les paramètres SQL. #informatique',CURRENT_TIMESTAMP - INTERVAL 13 HOUR,4);
INSERT INTO TOUITE_TAG VALUES (4,1);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (5,'La place Stanislas au petit matin. #nancy #photo',CURRENT_TIMESTAMP - INTERVAL 12 HOUR,1);
INSERT INTO TOUITE_TAG VALUES (5,2);
INSERT INTO TOUITE_TAG VALUES (5,3);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (6,'Quel est votre jeu préféré ? #jeux',CURRENT_TIMESTAMP - INTERVAL 11 HOUR,2);
INSERT INTO TOUITE_TAG VALUES (6,4);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (7,'Aujourd’hui, on sépare les actions du repository. #informatique',CURRENT_TIMESTAMP - INTERVAL 10 HOUR,3);
INSERT INTO TOUITE_TAG VALUES (7,1);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (8,'Une belle lumière sur les toits de #nancy. #photo',CURRENT_TIMESTAMP - INTERVAL 9 HOUR,4);
INSERT INTO TOUITE_TAG VALUES (8,2);
INSERT INTO TOUITE_TAG VALUES (8,3);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (9,'Petit rappel : pensez à tester les cas limites ! #informatique',CURRENT_TIMESTAMP - INTERVAL 8 HOUR,1);
INSERT INTO TOUITE_TAG VALUES (9,1);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (10,'Une soirée jeux entre amis. #jeux #nancy',CURRENT_TIMESTAMP - INTERVAL 7 HOUR,2);
INSERT INTO TOUITE_TAG VALUES (10,2);
INSERT INTO TOUITE_TAG VALUES (10,4);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (11,'Les tags permettent de suivre ses sujets favoris. #informatique',CURRENT_TIMESTAMP - INTERVAL 6 HOUR,3);
INSERT INTO TOUITE_TAG VALUES (11,1);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (12,'Le projet avance : rendez-vous sur mon mur ! #nancy',CURRENT_TIMESTAMP - INTERVAL 5 HOUR,4);
INSERT INTO TOUITE_TAG VALUES (12,2);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (13,'Une pause photo après les cours. #photo',CURRENT_TIMESTAMP - INTERVAL 4 HOUR,1);
INSERT INTO TOUITE_TAG VALUES (13,3);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (14,'On a enfin compris les déclencheurs SQL ! #informatique #nancy',CURRENT_TIMESTAMP - INTERVAL 3 HOUR,2);
INSERT INTO TOUITE_TAG VALUES (14,2);
INSERT INTO TOUITE_TAG VALUES (14,1);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (15,'Les histoires des jeux me fascinent. #jeux',CURRENT_TIMESTAMP - INTERVAL 2 HOUR,3);
INSERT INTO TOUITE_TAG VALUES (15,4);
INSERT INTO TOUITE(id_touite,contenu,date_publication,id_utilisateur) VALUES (16,'Un dernier touite pour tester la pagination. #informatique',CURRENT_TIMESTAMP - INTERVAL 1 HOUR,4);
INSERT INTO TOUITE_TAG VALUES (16,1);
INSERT INTO SUIVRE VALUES (1,2),(1,3),(2,3),(3,2),(4,2);
INSERT INTO ABONNEMENT_TAG VALUES (1,1),(2,3),(3,4);
INSERT INTO VOTE VALUES (1,2,1),(2,2,1),(3,2,-1),(2,1,1);
UPDATE UTILISATEUR u SET nb_touites =
    (SELECT COUNT(*) FROM TOUITE t WHERE t.id_utilisateur = u.id_utilisateur);
COMMIT;
