<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;

class ListerUtilisateursAction extends Action {
    public function execute(): string {
        $repo         = new UtilisateurRepository();
        $utilisateurs = $repo->tous();
        $html         = '<h1>Les utilisateurs</h1>
            <section class="card">';
        foreach ($utilisateurs as $u) {
            $html .= '<p>
                <a href="?action=author&id=' . $u->id . '">' . htmlspecialchars($u->nomComplet()) . '</a>';
                $html .= ' · ' . $u->nbTouites . ' touite(s)
                    </p>';
            }
            return $html . '
                </section>';
        }
    }
