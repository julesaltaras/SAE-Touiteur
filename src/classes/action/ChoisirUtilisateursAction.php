<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;

class ChoisirUtilisateurAction extends Action
{
    public function execute(): string
    {
        Action::verifierEnvoi();
        $id = Action::entier('user_id', $_POST);
        $repo = new UtilisateurRepository();
        $utilisateur = $repo->trouver($id);
        $_SESSION['user_id'] = $utilisateur->id;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: ?action=wall', true, 303);
        exit;
        return '';
    }
}
