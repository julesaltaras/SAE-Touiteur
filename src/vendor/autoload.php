<?php
spl_autoload_register(function ($classe) {

    $prefixe = 'iutnc\\touiteur\\';

    if (strpos($classe, $prefixe) !== 0) {

        return;
    }

    $classe = substr($classe, strlen($prefixe));

    $classe = str_replace('model\\', 'classes principales\\', $classe);

    $fichier = __DIR__ . '/../classes/' . str_replace('\\', '/', $classe) . '.php';

    if (is_file($fichier)) {

        require_once $fichier;
    }
});

/**
 * C fait les gars
 */