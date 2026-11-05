<?php

session_start();


date_default_timezone_set('Europe/Paris');


require __DIR__ . '/vendor/autoload.php';

if (\iutnc\touiteur\config\Connexion::obtenir() === null) {
    require __DIR__ . '/apercu.php';
    exit;
}

use iutnc\touiteur\dispatcher\Dispatcher;

$dispatcher = new Dispatcher();

$dispatcher->run();
