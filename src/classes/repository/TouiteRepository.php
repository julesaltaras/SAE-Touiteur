<?php
namespace iutnc\touiteur\repository;

use iutnc\touiteur\model\Touite;
use iutnc\touiteur\config\Connexion;

class TouiteRepository extends Repository
{
    public function trouver(int $id): Touite
    {
        $sql = 'SELECT * FROM TOUITE WHERE id_touite = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        $ligne = $requete->fetch();

        if (!$ligne) {
            throw new \DomainException('Touite introuvable.');
        }
        $utilisateurRepository = new UtilisateurRepository();
        $auteur = $utilisateurRepository->trouver((int) $ligne['id_utilisateur']);
        $score = $this->score($id);
        $tagRepository = new TagRepository();
        $tags = $tagRepository->pourTouite($id);
        $image = $this->imageTouite($id);
        return new Touite($id, (string) $ligne['contenu'], (string) $ligne['date_publication'], $auteur, $score, $tags, $image);
    }

    public function ajouter(int $auteur, string $texte): int
    {
        $sql = 'INSERT INTO TOUITE (contenu, date_publication, id_utilisateur) VALUES (?, CURRENT_TIMESTAMP, ?)';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$texte, $auteur]);
        // MySQL vient de creer cet identifiant.
        return (int) $this->pdo->lastInsertId();
    }

    public function nombreAujourdhui(int $auteur): int
    {
        $sql = 'SELECT COUNT(*) AS nombre FROM TOUITE WHERE id_utilisateur = ? AND DATE(date_publication) = CURRENT_DATE';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$auteur]);
        $ligne = $requete->fetch();
        return $ligne['nombre'];
    }

    public function lister(string $mode, int $id, int $page): array
    {
        $sql = 'SELECT t.id_touite FROM TOUITE t';
        $parametres = [];
        if ($mode === 'author') {
            $sql .= ' WHERE t.id_utilisateur = ?';
            $parametres = [$id];
        } elseif ($mode === 'tag') {
            $sql .= ' WHERE t.id_touite IN (SELECT id_touite FROM TOUITE_TAG WHERE id_tag = ?)';
            $parametres = [$id];
        } elseif ($mode === 'wall') {
            $sql .= ' WHERE t.id_utilisateur IN (SELECT id_utilisateur_suivi FROM SUIVRE WHERE id_utilisateur_abonne = ?)
                OR t.id_touite IN (SELECT tt.id_touite FROM TOUITE_TAG tt
                JOIN ABONNEMENT_TAG a ON a.id_tag = tt.id_tag WHERE a.id_utilisateur = ?)';
            $parametres = [$id, $id];
        }
        $debut = ($page - 1) * 10;
        $sql .= ' ORDER BY t.date_publication DESC, t.id_touite DESC LIMIT 11 OFFSET ' . (int) $debut;
        $requete = $this->pdo->prepare($sql);
        $requete->execute($parametres);
        $lignes = $requete->fetchAll();
        $touites = [];
        foreach ($lignes as $ligne) {
            if (count($touites) === 10) {
                break;
            }
            $touites[] = $this->trouver($ligne['id_touite']);
        }
        return ['items' => $touites, 'next' => count($lignes) > 10];
    }

    public function publier(int $auteur, string $texte, ?string $image, string $description): int
    {
        Touite::verifierContenu($texte);
        $libelles = Touite::extraireTags($texte);
        if (mb_strlen($description, 'UTF-8') > 50) {
            throw new \DomainException('Description de l’image : 50 caractères maximum.');
        }
        $utilisateurs = new UtilisateurRepository();
        $utilisateurs->trouver($auteur);
        $tags = new TagRepository();
        // Tout enregistrer ensemble, ou tout annuler en cas d’erreur.
        $this->pdo->beginTransaction();
        try {
            if ($this->nombreAujourdhui($auteur) >= 50) {
                throw new \DomainException('Vous avez atteint 50 touites aujourd’hui.');
            }
            $id = $this->ajouter($auteur, $texte);
            foreach ($libelles as $libelle) {
                $tags->associer($id, $tags->obtenirId($libelle));
            }
            if ($image !== null) {
                $this->ajouterImage($id, $image, $description);
            }
            $utilisateurs->actualiserCompteur($auteur);
            $this->pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            throw $e;
        }
    }

    public function score(int $touite): int
    {
        if (!Connexion::$votes) {
            return 0;
        }
        $requete = $this->pdo->prepare('SELECT valeur FROM VOTE WHERE id_touite = ?');
        $requete->execute([$touite]);
        $score = 0;
        foreach ($requete->fetchAll() as $vote) {
            $score = $score + $vote['valeur'];
        }
        return $score;
    }

    public function dejaVote(int $user, int $touite): bool
    {
        if (!Connexion::$votes) {
            return false;
        }
        $sql = 'SELECT 1 FROM VOTE WHERE id_utilisateur = ? AND id_touite = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$user, $touite]);
        return $requete->fetch() !== false;
    }

    public function voter(int $user, int $touite, int $valeur): void
    {
        if ($valeur !== 1 && $valeur !== -1) {
            throw new \DomainException('Vote invalide.');
        }
        $this->trouver($touite);
        if ($this->dejaVote($user, $touite)) {
            throw new \DomainException('Vous avez déjà évalué ce touite.');
        }

        if (!Connexion::$votes) {
            throw new \DomainException('La table VOTE manque. Exécutez le premier bloc de base.sql.');
        }
        try {
            $sql = 'INSERT INTO VOTE VALUES (?, ?, ?)';
            $requete = $this->pdo->prepare($sql);
            $requete->execute([$user, $touite, $valeur]);
        } catch (\PDOException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                throw new \DomainException('Vous avez déjà évalué ce touite.');
            }
            throw $e;
        }
    }

    public function trouverImage(int $id): ?array
    {
        $sql = 'SELECT * FROM IMAGE WHERE id_image = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        $image = $requete->fetch();
        if ($image === false) {
            return null;
        }
        return $image;
    }

    public function imageTouite(int $id): ?array
    {
        $sql = 'SELECT * FROM IMAGE WHERE id_touite = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        $image = $requete->fetch();
        if ($image === false) {
            return null;
        }
        return $image;
    }

    public function ajouterImage(int $touite, string $fichier, string $description): void
    {
        $sql = 'INSERT INTO IMAGE (description, chemin_fichier, id_touite) VALUES (?, ?, ?)';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$description, $fichier, $touite]);
    }

}
