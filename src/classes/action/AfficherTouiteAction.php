<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\renderer\PageRenderer;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\renderer\TouiteRenderer;

class AfficherTouiteAction extends Action
{
    public function execute(): string
    {
        $id = Action::entier('id', $_GET);
        $touiteRepository = new TouiteRepository();
        $touite = $touiteRepository->trouver($id);
        $renderer = new TouiteRenderer($touite, true);
        $html = '<h1>Le touite en détail</h1>' . $renderer->render();
        $html .= '<section class="card"><h2>Participer</h2>';
        $votes = new TouiteRepository();
        if ($votes->dejaVote((int) $_SESSION['user_id'], $id)) {
            $html .= '<p>Votre vote est enregistré. Un seul vote par utilisateur et par touite.</p>';
        } else {
            $html .= PageRenderer::bouton('vote', 'J’aime (+1)', ['id' => $id, 'value' => 1]);
            $html .= PageRenderer::bouton('vote', 'Je n’aime pas (−1)', ['id' => $id, 'value' => -1]);
        }
        $html .= TouiteRenderer::boutonSuivi($touite->auteur->id);
        return $html . '</section>';
    }
}
