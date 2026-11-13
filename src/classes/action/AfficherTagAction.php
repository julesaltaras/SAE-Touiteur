<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\renderer\PageRenderer;

use iutnc\touiteur\renderer\TouiteRenderer;
use iutnc\touiteur\repository\TagRepository;
use iutnc\touiteur\repository\TouiteRepository;

class AfficherTagAction extends Action
{
    public function execute(): string
    {
        $id = Action::entier('id', $_GET);
        $tagRepository = new TagRepository();
        $tag = $tagRepository->trouver($id);
        $page = 1;
        if (isset($_GET['page'])) {
            $page = Action::entier('page', $_GET);
        }
        $touiteRepository = new TouiteRepository();
        $resultat = $touiteRepository->lister('tag', $id, $page);
        $html = '<h1>#' . htmlspecialchars($tag->libelle) . '</h1>';
        $html .= PageRenderer::bouton('subscribe', 'Suivre ce tag', ['tag' => $tag->libelle]);
        $html .= TouiteRenderer::liste($resultat, '?action=tag&id=' . $id, $page);
        return $html;
    }
}
