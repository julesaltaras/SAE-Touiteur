<?php
namespace iutnc\touiteur\config;

class Connexion {
    private static ?\PDO $pdo = null;
    public static bool $votes = false;

    public static function obtenir(): ?\PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }
        $config = parse_ini_file(__DIR__ . '/touiteur.ini');
        if ($config === false || empty($config['database'])) {
            return null;
        }
        $dsn = 'mysql:host=' . $config['host'] . ';port=' . $config['port'];
        $dsn .= ';dbname=' . $config['database'] . ';charset=utf8mb4';
        try {
            $pdo = new \PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            // Si une table manque, on affiche l'aperçu au lieu de tout casser.
            foreach (['TAG', 'IMAGE', 'SUIVRE', 'ABONNEMENT_TAG', 'TOUITE_TAG'] as $table) {
                $pdo->query('SELECT * FROM ' . $table . ' LIMIT 1');
            }
            $requete = $pdo->query('SELECT * FROM UTILISATEUR LIMIT 1');
            if ($requete->fetch() === false) {
                return null;
            }
            $requete     = $pdo->query("SHOW TABLES LIKE 'VOTE'");
            self::$votes = $requete->fetch() !== false;
            $pdo->query('SELECT * FROM TOUITE LIMIT 1');
            $pdo->exec("SET time_zone = '" . date('P') . "'");
            self::$pdo = $pdo;
            return $pdo;
        } catch (\PDOException $e) {
            return null;
        }
    }
}
