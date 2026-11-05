<?php
namespace iutnc\touiteur\repository;

use iutnc\touiteur\config\Connexion;

class Repository
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connexion::obtenir();
    }
}
