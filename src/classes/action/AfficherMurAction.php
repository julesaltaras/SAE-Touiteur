<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\renderer\PageRenderer;

use iutnc\touiteur\renderer\TouiteRenderer;
use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\repository\TagRepository;
use iutnc\touiteur\repository\TouiteRepository;

class AfficherMurAction extends Action
{
    public function execute(): string
    {
        $id = (int) $_SESSION['user_id'];
        $utilisateurRepository = new UtilisateurRepository();
        $utilisateur = $utilisateurRepository->trouver($id);
        $tagRepository = new TagRepository();
        $abonnements = $tagRepository->pourUtilisateur($id);
        $page = 1;
        if (isset($_GET['page'])) {
            $page = Action::entier('page', $_GET);
        }
        $touiteRepository = new TouiteRepository();
        $resultat = $touiteRepository->lister('wall', $id, $page);
        $html = '<h1>Le mur de ' . htmlspecialchars($utilisateur->prenom) . '</h1>';
        $html .= $this->abonnements($abonnements);
        $html .= TouiteRenderer::liste($resultat, '?action=wall', $page);
        return $html;
    }

    private function abonnements(array $abonnements): string
    {
        $jeton = Action::champCsrf();
        $html = <<<HTML
        <section class="card">
            <h2>Suivre un tag</h2>
            <form method="post" action="?action=subscribe">
                $jeton
                <label for="tag">Tag à suivre</label>
                <input id="tag" name="tag" maxlength="16" placeholder="#informatique" required>
                <button>S’abonner</button>
            </form>
        HTML;
        foreach ($abonnements as $tag) {
            $html .= '<p><a href="?action=tag&id=' . (int) $tag['id_tag'] . '">#' . htmlspecialchars($tag['libelle']) . '</a> ';
            $html .= PageRenderer::bouton('subscribe', 'Se désabonner', ['tag' => $tag['libelle'], 'remove' => '1']) . '</p>';
        }
        return $html . '</section>';
    }
}
