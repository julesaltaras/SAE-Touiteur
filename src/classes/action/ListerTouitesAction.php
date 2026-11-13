<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\renderer\TouiteRenderer;
use iutnc\touiteur\repository\TouiteRepository;

class ListerTouitesAction extends Action
{
    public function execute(): string
    {
        $page = 1;
        if (isset($_GET['page'])) {
            $page = Action::entier('page', $_GET);
        }
        $repo = new TouiteRepository();
        $resultat = $repo->lister('all', 0, $page);
        return '<h1>Tous les touites</h1>' . TouiteRenderer::liste($resultat, '?action=all', $page);
    }
}
