<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\renderer\TouiteRenderer;
use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\repository\TouiteRepository;

class AfficherAuteurAction extends Action
{
    public function execute(): string
    {
        $id = Action::entier('id', $_GET);
        $utilisateurRepository = new UtilisateurRepository();
        $auteur = $utilisateurRepository->trouver($id);
        $page = 1;
        if (isset($_GET['page'])) {
            $page = Action::entier('page', $_GET);
        }
        $touiteRepository = new TouiteRepository();
        $resultat = $touiteRepository->lister('author', $id, $page);
        $html = '<h1>' . htmlspecialchars($auteur->nomComplet()) . '</h1>';
        $html .= TouiteRenderer::boutonSuivi($id);
        $html .= TouiteRenderer::liste($resultat, '?action=author&id=' . $id, $page);
        return $html;
    }
}
