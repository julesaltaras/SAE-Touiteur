<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;

class AfficherProfilAction extends Action
{
    public function execute(): string
    {
        $id = (int) $_SESSION['user_id'];
        $repo = new UtilisateurRepository();
        $utilisateur = $repo->trouver($id);
        $abonnes = $repo->abonnes($id);
        $moyenne = $repo->moyenne($id);
        $id = $utilisateur->id;
        $score = 'Aucun touite';
        if ($moyenne !== null) {
            $score = number_format($moyenne, 2, ',', ' ');
        }
        $nom = htmlspecialchars($utilisateur->nomComplet());
        $nombre = count($abonnes);
        $html = <<<HTML
        <h1>$nom</h1>
        <section class="card">
            <h2>Mon activité</h2>
            <p>{$utilisateur->nbTouites} touite(s) et $nombre abonné(s).</p>
            <p>Score moyen de mes touites : $score</p>
            <a href="?action=author&id=$id">Voir mes touites</a>
        </section>
        <section class="card">
            <h2>Ils me suivent</h2>
        HTML;
        if (count($abonnes) === 0) {
            $html .= '<p>Aucun abonné pour le moment.</p>';
        }
        foreach ($abonnes as $abonne) {
            $html .= '<p><a href="?action=author&id=' . (int) $abonne['id_utilisateur'] . '">';
            $html .= htmlspecialchars($abonne['prenom'] . ' ' . $abonne['nom']) . '</a></p>';
        }
        return $html . '</section>';
    }
}
