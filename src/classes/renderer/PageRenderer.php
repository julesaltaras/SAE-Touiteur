<?php
namespace iutnc\touiteur\renderer;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\repository\UtilisateurRepository;

class PageRenderer
{
    private string $contenu;

    public function __construct(string $contenu)
    {
        $this->contenu = $contenu;
    }

    public function render(): string
    {
        $repo = new UtilisateurRepository();
        $utilisateurs = $repo->tous();
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Touiteur</title>
        <link rel="stylesheet" href="CSS/style.css">

        <body>
            <header>
                <h1>Touiteur</h1>
                <nav>
                    <a href="?action=all">Tous les touites</a> · <a href="?action=wall">Mon mur</a> ·
                    <a href="?action=publish">Publier</a> · <a href="?action=profile">Mon profil</a> ·
                    <a href="?action=users">Utilisateurs</a> · <a href="?action=influencers">Influenceurs</a> ·
                    <a href="admin.php">Administration</a>
                </nav>
                <form method="post" action="?action=choose">
                    <?= Action::champCsrf() ?>
                    <label>Utilisateur : <select id="user" name="user_id">
                            <?php foreach ($utilisateurs as $u): ?>
                                <option value="<?= $u->id ?>" <?php if ($u->id === (int) $_SESSION['user_id']) {
                                      echo 'selected';
                                  } ?>>
                                    <?= htmlspecialchars($u->nomComplet()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select></label>
                    <button>Changer</button>
                </form>
            </header>
            <main>
                <?= $this->contenu ?>
            </main>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }

    public static function classement(array $lignes, bool $tags = false): string
    {
        $html = '<table><tr><th>Nom</th><th>Nombre</th></tr>';
        foreach ($lignes as $ligne) {
            if ($tags) {
                $nom = '#' . $ligne['libelle'];
                $url = '?action=tag&id=' . $ligne['id_tag'];
            } else {
                $nom = $ligne['prenom'] . ' ' . $ligne['nom'];
                $url = '?action=author&id=' . $ligne['id_utilisateur'];
            }
            $html .= '<tr><td><a href="index.php' . $url . '">' . htmlspecialchars($nom) . '</a></td>';
            $html .= '<td>' . (int) $ligne['total'] . '</td></tr>';
        }
        return $html . '</table>';
    }

    public static function bouton(string $action, string $bouton, array $champs = []): string
    {
        $html = '<form class="inline" method="post" action="?action=' . htmlspecialchars($action) . '">';
        $html .= Action::champCsrf();
        foreach ($champs as $nom => $valeur) {
            $html .= '<input type="hidden" name="' . htmlspecialchars($nom) . '" value="' . htmlspecialchars($valeur) . '">';
        }
        return $html . '<button>' . htmlspecialchars($bouton) . '</button></form>';
    }
}
