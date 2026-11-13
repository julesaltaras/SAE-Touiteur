<?php
namespace iutnc\touiteur\action;

abstract class Action
{
    protected string $http_method;

    public function __construct()
    {
        $this->http_method = 'GET';
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->http_method = $_SERVER['REQUEST_METHOD'];
        }
    }

    abstract public function execute(): string;

    public static function texte(string $cle, array $tableau): string
    {
        if (!isset($tableau[$cle])) {
            return '';
        }
        if (!is_string($tableau[$cle])) {
            throw new \DomainException('Champ invalide : ' . $cle);
        }
        return trim($tableau[$cle]);
    }

    public static function entier(string $cle, array $tableau): int
    {
        $texte = self::texte($cle, $tableau);
        $nombre = filter_var($texte, FILTER_VALIDATE_INT);
        if ($nombre === false || $nombre < 1 || $nombre > 2147483647) {
            throw new \DomainException('Identifiant invalide.');
        }
        return $nombre;
    }

    public static function champCsrf(): string
    {
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
    }

    public static function verifierEnvoi(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            throw new \DomainException('Utilisez le formulaire pour effectuer cette action.');
        }
        $jeton = Action::texte('csrf', $_POST);
        if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $jeton)) {
            http_response_code(403);
            throw new \DomainException('Formulaire expiré. Rechargez la page.');
        }
    }
}
