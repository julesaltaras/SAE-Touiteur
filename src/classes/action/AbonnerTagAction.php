<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TagRepository;

class AbonnerTagAction extends Action {
    public function execute(): string {
        Action::verifierEnvoi();
        $tag     = Action::texte('tag', $_POST);
        $retirer = Action::texte('remove', $_POST) === '1';
        $repo    = new TagRepository();
        $repo->abonner((int) $_SESSION['user_id'], $tag, $retirer);
        header('Location: ?action=wall', true, 303);
        exit;
        return '';
    }
}
