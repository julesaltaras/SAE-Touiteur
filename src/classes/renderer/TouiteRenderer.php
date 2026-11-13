<?php
namespace iutnc\touiteur\renderer;

use iutnc\touiteur\model\Touite;
use iutnc\touiteur\repository\UtilisateurRepository;

class TouiteRenderer
{
    private Touite $touite;
    private bool $detail;

    public function __construct(Touite $touite, bool $detail = false)
    {
        $this->touite = $touite;
        $this->detail = $detail;
    }

    public function render(): string
    {
        $t = $this->touite;
        $texte = $t->contenu;
        if (!$this->detail && mb_strlen($texte, 'UTF-8') > 140) {
            $texte = mb_substr($texte, 0, 140, 'UTF-8') . '…';
        }
        ob_start();
        ?>
        <article class=" carte touite">
            <header>
                <a class="author" href="?action=author&id=<?= $t->auteur->id ?>">
                    <?= htmlspecialchars($t->auteur->nomComplet()) ?>
                </a>
                <time><?= htmlspecialchars($t->date) ?></time>
            </header>
            <p class="content"><?= htmlspecialchars($texte) ?></p>
            <div class="tags">
                <?php foreach ($t->tags as $tag): ?>
                    <a class="chip" href="?action=tag&id=<?= $tag->id ?>">#<?= htmlspecialchars($tag->libelle) ?></a>
                <?php endforeach; ?>
            </div>
                        <?php if ($t->image !== null): ?>
                <img class="touite-image" src="image.php?id=<?= (int) $t->image['id_image'] ?>"
                    alt="<?= htmlspecialchars($t->image['description']) ?>">

                <p><?= htmlspecialchars($t->image['description']) ?></p>
                        <?php endif; ?>
            <footer>
                <span>Like : <strong><?= $t->score ?></strong>
                </span>
                <?php if (!$this->detail): ?>
                    <a href="?action=detail&id=<?= $t->id ?>">Voir le touite →</a>
                <?php endif; ?>
            </footer>
        </article>
        <?php
        return ob_get_clean();
    }

    public static function liste(array $resultat, string $url, int $page): string
    {
        $html = '';
        foreach ($resultat['items'] as $touite) {
            $renderer = new TouiteRenderer($touite);
            $html .= $renderer->render();
        }
        if (count($resultat['items']) === 0) {
            $html .= '<p class="card">Aucun touite ici. Vous pouvez suivre des auteurs ou des tags.</p>';
        }
        $html .= '<nav class="pagination" aria-label="Pagination">';
        if ($page > 1) {
            $html .= '<a href="' . htmlspecialchars($url . '&page=' . ($page - 1)) . '">Précédente</a>';
        }
        $html .= '<span>Page ' . $page . '</span>';
        if ($resultat['next']) {
            $html .= '<a href="' . htmlspecialchars($url . '&page=' . ($page + 1)) . '">Suivante</a>';
        }
        return $html . '
                </nav>';
    }

    public static function boutonSuivi(int $auteur): string
    {
        if ($auteur === (int) $_SESSION['user_id']) {
            return '';
        }
        $utilisateurRepository = new UtilisateurRepository();
        $suivi = $utilisateurRepository->suit((int) $_SESSION['user_id'], $auteur);
        $texte = 'Suivre cet auteur';
        $retirer = '0';
        if ($suivi) {
            $texte = 'Ne plus suivre';
            $retirer = '1';
        }
        return PageRenderer::bouton('follow', $texte, ['id' => $auteur, 'remove' => $retirer]);
    }
}
