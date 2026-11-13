<?php
use iutnc\touiteur\renderer\PageRenderer;
session_start();
date_default_timezone_set('Europe/Paris');
require __DIR__ . '/vendor/autoload.php';

if (\iutnc\touiteur\config\Connexion::obtenir() === null) {
    require __DIR__ . '/apercu.php';
    exit;
}

use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\repository\TagRepository;

$utilisateurs = new UtilisateurRepository();
$tags         = new TagRepository();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Touiteur - Administration</title>
        <link rel="stylesheet" href="CSS/style.css">
    </head>
    <body>
        <main class="admin">
            <a href="index.php">Retour au site</a>
            <h1>Administration</h1>
            <h2>Influenceurs</h2>
            <p>Utilisateurs classés par nombre d'abonnés croissant.</p>
                <?= PageRenderer::classement($utilisateurs->influenceurs()) ?>
                <h2>Tendances</h2>
                <p>Tags classés par nombre de touites croissant.</p>
                <?= PageRenderer::classement($tags->tendances(), true) ?>
                </main>
                </body>
                </html>
