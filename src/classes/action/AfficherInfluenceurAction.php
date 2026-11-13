<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\renderer\PageRenderer;

class AfficherInfluenceursAction extends Action
{
    public function execute(): string
    {
        $repo = new UtilisateurRepository();
        $lignes = $repo->influenceurs();
        $html = '<h1>Influenceurs</h1><p>Nombre de suiveurs croissant.</p>';
        return $html . PageRenderer::classement($lignes);
    }
}
