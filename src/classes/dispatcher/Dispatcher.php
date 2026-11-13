<?php
namespace iutnc\touiteur\dispatcher;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\renderer\PageRenderer;
use iutnc\touiteur\repository\UtilisateurRepository;

use iutnc\touiteur\action\ListerTouitesAction;
use iutnc\touiteur\action\AfficherAuteurAction;
use iutnc\touiteur\action\AfficherTagAction;
use iutnc\touiteur\action\AfficherMurAction;
use iutnc\touiteur\action\AfficherTouiteAction;
use iutnc\touiteur\action\AfficherProfilAction;
use iutnc\touiteur\action\ListerUtilisateursAction;
use iutnc\touiteur\action\AfficherInfluenceursAction;
use iutnc\touiteur\action\PublierAction;
use iutnc\touiteur\action\ChoisirUtilisateurAction;
use iutnc\touiteur\action\SuivreUtilisateurAction;
use iutnc\touiteur\action\AbonnerTagAction;
use iutnc\touiteur\action\VoterAction;

class Dispatcher
{
    public function run(): void
    {
        $utilisateurRepository = new UtilisateurRepository();
        $utilisateurs = $utilisateurRepository->tous();
        $ids = [];
        foreach ($utilisateurs as $utilisateur) {
            $ids[] = $utilisateur->id;
        }
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_id'], $ids)) {
            $_SESSION['user_id'] = $utilisateurs[0]->id;
        }
        try {
            $nom = Action::texte('action', $_GET);
            if ($nom === '') {
                $nom = 'all';
            }
            switch ($nom) {
                case 'all':
                    $action = new ListerTouitesAction();
                    break;
                case 'author':
                    $action = new AfficherAuteurAction();
                    break;
                case 'tag':
                    $action = new AfficherTagAction();
                    break;
                case 'wall':
                    $action = new AfficherMurAction();
                    break;
                case 'detail':
                    $action = new AfficherTouiteAction();
                    break;
                case 'profile':
                    $action = new AfficherProfilAction();
                    break;
                case 'users':
                    $action = new ListerUtilisateursAction();
                    break;
                case 'influencers':
                    $action = new AfficherInfluenceursAction();
                    break;
                case 'publish':
                    $action = new PublierAction();
                    break;
                case 'choose':
                    $action = new ChoisirUtilisateurAction();
                    break;
                case 'follow':
                    $action = new SuivreUtilisateurAction();
                    break;
                case 'subscribe':
                    $action = new AbonnerTagAction();
                    break;
                case 'vote':
                    $action = new VoterAction();
                    break;
                default:
                    throw new \DomainException('Cette page n’existe pas bro.');
            }
            $contenu = $action->execute();
        } catch (\DomainException $e) {
            if (http_response_code() < 400) {
                http_response_code(400);
            }
            $contenu = '<h1>Action impossible</h1><p class="error">' . htmlspecialchars($e->getMessage()) . '</p>';
        } catch (\Throwable $e) {
            error_log((string) $e);
            http_response_code(500);

            $contenu = '<h1>Erreur</h1><p>'
                . htmlspecialchars($e->getMessage())
                . '</p>';
        }
        $page = new PageRenderer($contenu);
        echo $page->render();
    }
}
