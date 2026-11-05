<?php
namespace iutnc\touiteur\repository;

use iutnc\touiteur\model\Utilisateur;
use iutnc\touiteur\config\Connexion;

class UtilisateurRepository extends Repository {
    public function tous(): array {
        $sql     = 'SELECT id_utilisateur FROM UTILISATEUR ORDER BY prenom, nom';
        $requete = $this->pdo->prepare($sql);
        $requete->execute();
        $lignes = $requete->fetchAll();

        $resultat = [];
        foreach ($lignes as $ligne) {
            $resultat[] = $this->trouver($ligne['id_utilisateur']);
        }
        return $resultat;
    }

    public function trouver(int $id): Utilisateur {
        $sql     = 'SELECT * FROM UTILISATEUR WHERE id_utilisateur = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        $ligne = $requete->fetch();

        if (!$ligne) {
            throw new \DomainException('Utilisateur introuvable.');
        }
        return new Utilisateur($id, (string) $ligne['nom'], (string) $ligne['prenom'],
        (string) $ligne['email'], (int) $ligne['nb_touites']);
    }

    public function actualiserCompteur(int $id): void {
        $sql = 'UPDATE UTILISATEUR SET nb_touites =
            (SELECT COUNT(*) FROM TOUITE WHERE id_utilisateur = ?) WHERE id_utilisateur = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id, $id]);
    }

    public function suit(int $user, int $auteur): bool {
        $sql = 'SELECT 1 FROM SUIVRE WHERE id_utilisateur_abonne = ?
            AND id_utilisateur_suivi = ?';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$user, $auteur]);
        return $requete->fetch() !== false;
    }

    public function suivre(int $user, int $auteur, bool $retirer): void {
        $abonne = $this->trouver($user);
        $suivi  = $this->trouver($auteur);
        if ($user === $auteur || strcasecmp($abonne->email, $suivi->email) === 0) {
            throw new \DomainException('Vous ne pouvez pas suivre ce profil.');
        }
        if ($retirer) {
            $sql     = 'DELETE FROM SUIVRE WHERE id_utilisateur_abonne = ? AND id_utilisateur_suivi = ?';
            $requete = $this->pdo->prepare($sql);
            $requete->execute([$user, $auteur]);
        } elseif (!$this->suit($user, $auteur)) {
            $sql     = 'INSERT INTO SUIVRE VALUES (?, ?)';
            $requete = $this->pdo->prepare($sql);
            $requete->execute([$user, $auteur]);
        }
    }

    public function abonnes(int $id): array {
        $sql = 'SELECT u.* FROM UTILISATEUR u JOIN SUIVRE s ON s.id_utilisateur_abonne = u.id_utilisateur
            WHERE s.id_utilisateur_suivi = ? ORDER BY u.prenom, u.nom';
        $requete = $this->pdo->prepare($sql);
        $requete->execute([$id]);
        return $requete->fetchAll();
    }

    public function moyenne(int $auteur): ?float {
        $requete = $this->pdo->prepare('SELECT id_touite FROM TOUITE WHERE id_utilisateur = ?');
        $requete->execute([$auteur]);
        $touites = $requete->fetchAll();
        if (count($touites) === 0) {
            return null;
        }
        $repo  = new TouiteRepository();
        $somme = 0;
        foreach ($touites as $touite) {
            $somme = $somme + $repo->score($touite['id_touite']);
        }
        return $somme / count($touites);
    }

    public function influenceurs(): array {
        $sql = 'SELECT u.id_utilisateur, u.nom, u.prenom, COUNT(s.id_utilisateur_abonne) AS total
            FROM UTILISATEUR u LEFT JOIN SUIVRE s ON s.id_utilisateur_suivi = u.id_utilisateur
            GROUP BY u.id_utilisateur, u.nom, u.prenom ORDER BY total ASC, u.id_utilisateur ASC';
        $requete = $this->pdo->prepare($sql);
        $requete->execute();
        return $requete->fetchAll();
    }

}
