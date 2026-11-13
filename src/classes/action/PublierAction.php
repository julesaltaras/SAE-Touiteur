<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\model\Touite;
use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\repository\TouiteRepository;

class PublierAction extends Action
{
    public function execute(): string
    {
        $utilisateurRepository = new UtilisateurRepository();
        $utilisateur = $utilisateurRepository->trouver((int) $_SESSION['user_id']);
        $texte = '';
        $description = '';
        $erreur = '';
        if ($this->http_method === 'POST') {
            Action::verifierEnvoi();
            $image = null;
            try {
                $texte = Action::texte('content', $_POST);
                $description = Action::texte('description', $_POST);
                Touite::verifierContenu($texte);
                Touite::extraireTags($texte);
                if (mb_strlen($description, 'UTF-8') > 50) {
                    throw new \DomainException('Description de l’image : 50 caractères maximum.');
                }
                if (isset($_FILES['image'])) {
                    $image = $this->enregistrerImage($_FILES['image']);
                }
                $repository = new TouiteRepository();
                $id = $repository->publier($utilisateur->id, $texte, $image, $description);
                header('Location: ?action=detail&id=' . $id, true, 303);
                exit;
            } catch (\Throwable $e) {
                if ($image !== null) {
                    unlink(dirname(__DIR__, 2) . '/images/' . $image);
                }
                if (!$e instanceof \DomainException) {
                    throw $e;
                }
                $erreur = $e->getMessage();
            }
        }
        return $this->formulaire($utilisateur->nomComplet(), $texte, $description, $erreur);
    }

    private function formulaire(string $nom, string $texte, string $description, string $erreur): string
    {
        if ($erreur !== '') {
            $erreur = '<p class="error">' . htmlspecialchars($erreur) . '</p>';
        }
        $nom = htmlspecialchars($nom);
        $texte = htmlspecialchars($texte);
        $description = htmlspecialchars($description);
        $jeton = Action::champCsrf();
        return <<<HTML
        <h1>Publier un touite</h1>
        <section class="card">
            $erreur
            <p>Auteur : <strong>$nom</strong></p>
            <form method="post" enctype="multipart/form-data" action="?action=publish">
                $jeton
                <label for="content">Texte (235 caractères maximum)</label>
                <textarea id="content" name="content" rows="5" maxlength="235" required>$texte</textarea>
                <p>Ajoutez des #tags dans votre texte, jusqu’à 15 caractères par tag.</p>
                <label for="image">Image facultative : JPEG, PNG ou WebP, 5 Mo maximum</label>
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <label for="description">Description de l’image</label>
                <input id="description" name="description" maxlength="50" value="$description">
                <button>Publier</button>
            </form>
        </section>
        HTML;
    }

    private function enregistrerImage(array $fichier): ?string
    {
        if ($fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($fichier['error'] !== UPLOAD_ERR_OK || $fichier['size'] > 5 * 1024 * 1024) {
            throw new \DomainException('Image non reçue ou supérieure à 5 Mo.');
        }
        $info = new \finfo(FILEINFO_MIME_TYPE);
        $type = $info->file($fichier['tmp_name']);
        $formats = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($formats[$type]) || !getimagesize($fichier['tmp_name'])) {
            throw new \DomainException('Choisissez une image JPEG, PNG ou WebP.');
        }
        $dossier = dirname(__DIR__, 2) . '/images/';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }
        $nom = bin2hex(random_bytes(16)) . '.' . $formats[$type];
        if (!move_uploaded_file($fichier['tmp_name'], $dossier . $nom)) {
            throw new \DomainException('Impossible de sauvegarder l’image.');
        }
        return $nom;
    }
}
