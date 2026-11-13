<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class VoterAction extends Action {
    public function execute(): string {
        Action::verifierEnvoi();
        $id     = Action::entier('id', $_POST);
        $valeur = Action::texte('value', $_POST);
        if ($valeur !== '1' && $valeur !== '-1') {
            throw new \DomainException('Vote invalide.');
        }
        $repo = new TouiteRepository();
        $repo->voter((int) $_SESSION['user_id'], $id, (int) $valeur);
        header('Location: ?action=detail&id=' . $id, true, 303);
        exit;
        return '';
    }
}
