<?php
use iutnc\touiteur\action\Action;
session_start();
require __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\repository\TouiteRepository;

try {
    if (\iutnc\touiteur\config\Connexion::obtenir() === null) {
        throw new Exception('Base indisponible.');
    }
    $id = Action::entier('id', $_GET);
    $touiteRepository = new TouiteRepository();
    $image = $touiteRepository->trouverImage($id);
    if (!$image || basename($image['chemin_fichier']) !== $image['chemin_fichier']) {
        throw new Exception('Image introuvable.');
    }
    $fichier = __DIR__ . '/images' . '/' . $image['chemin_fichier'];
    if (!is_file($fichier)) {
        throw new Exception('Image introuvable.');
    }
    $info = new finfo(FILEINFO_MIME_TYPE);
    $type = $info->file($fichier);
    if (!in_array($type, ['image/jpeg', 'image/png', 'image/webp'])) {
        throw new Exception('Format invalide.');
    }
    header('Content-Type: ' . $type);
    header('X-Content-Type-Options: nosniff');
    header('Content-Length: ' . filesize($fichier));
    session_write_close();
    readfile($fichier);
} catch (Throwable $e) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Image introuvable.';
}
