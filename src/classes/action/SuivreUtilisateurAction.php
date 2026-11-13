<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;

class SuivreUtilisateurAction extends Action {
    public function execute(): string {
        Action::verifierEnvoi();
        $id      = Action::entier('id', $_POST);
        $retirer = Action::texte('remove', $_POST) === '1';
        $repo    = new UtilisateurRepository();
        $repo->suivre((int) $_SESSION['user_id'], $id, $retirer);
        header('Location: ?action=author&id=' . $id, true, 303);
        exit;
        return '';
    }
}
