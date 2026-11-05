<?php
namespace iutnc\touiteur\repository;

use iutnc\touiteur\model\Tag;
use iutnc\touiteur\model\Touite;

class TagRepository extends Repository
{
    public function trouver(int $id): Tag
    {
        $sql = 'SELECT * FROM TAG WHERE id_tag = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        $ligne = $requete->fetch();

        if (!$ligne) {
            throw new \DomainException('Tag introuvable.');
        }
        return new Tag($id, $ligne['libelle'], (string) $ligne['description']);
    }

    public function obtenirId(string $libelle): int
    {
        $requete = $this->pdo->prepare('SELECT id_tag FROM TAG WHERE libelle = ?');
        $requete->execute([$libelle]);
        $tag = $requete->fetch();
        if ($tag !== false) {
            return $tag['id_tag'];
        }
        $requete = $this->pdo->prepare('INSERT INTO TAG (libelle, description) VALUES (?, ?)');
        $requete->execute([$libelle, 'Tag #' . $libelle]);
        return (int) $this->pdo->lastInsertId();
    }

    public function pourTouite(int $id): array
    {
        $sql = 'SELECT id_tag FROM TOUITE_TAG WHERE id_touite = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        $lignes = $requete->fetchAll();

        $tags = [];
        foreach ($lignes as $ligne) {
            $tags[] = $this->trouver($ligne['id_tag']);
        }
        return $tags;
    }

    public function associer(int $touite, int $tag): void
    {
        $sql = 'INSERT INTO TOUITE_TAG VALUES (?, ?)';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$touite, $tag]);
    }

    public function pourUtilisateur(int $user): array
    {
        $sql = 'SELECT t.* FROM TAG t JOIN ABONNEMENT_TAG a ON a.id_tag = t.id_tag
            WHERE a.id_utilisateur = ? ORDER BY t.libelle';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$user]);
        return $requete->fetchAll();
    }

    public function abonner(int $user, string $tag, bool $retirer): void
    {
        $tag = mb_strtolower(ltrim(trim($tag), '#'), 'UTF-8');
        $tags = Touite::extraireTags('#' . $tag);
        if (count($tags) !== 1 || $tags[0] !== $tag) {
            throw new \DomainException('Tag invalide : lettres, chiffres et _ uniquement.');
        }
        // Tout enregistrer ensemble, ou tout annuler en cas d’erreur.
        $this->pdo->beginTransaction();
        try {
            if ($retirer) {
                $sql = 'DELETE a FROM ABONNEMENT_TAG a JOIN TAG t ON t.id_tag = a.id_tag
                    WHERE a.id_utilisateur = ? AND t.libelle = ?';
                $requete = $this->pdo->prepare($sql);
                $requete->execute([$user, $tag]);
            } else {
                $id = $this->obtenirId($tag);
                $requete = $this->pdo->prepare('SELECT * FROM ABONNEMENT_TAG WHERE id_utilisateur = ? AND id_tag = ?');
                $requete->execute([$user, $id]);
                if ($requete->fetch() === false) {
                    $requete = $this->pdo->prepare('INSERT INTO ABONNEMENT_TAG VALUES (?, ?)');
                    $requete->execute([$user, $id]);
                }
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function tendances(): array
    {
        $sql = 'SELECT t.id_tag, t.libelle, COUNT(tt.id_touite) AS total FROM TAG t
            LEFT JOIN TOUITE_TAG tt ON tt.id_tag = t.id_tag GROUP BY t.id_tag, t.libelle
            ORDER BY total ASC, t.libelle ASC';
        $requete = $this->pdo->prepare($sql);
        $requete->execute();
        return $requete->fetchAll();
    }

}
