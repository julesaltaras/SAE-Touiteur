<?php
function titre($t) {
    echo PHP_EOL . "=== $t ===" . PHP_EOL;
}

function expmod($x, $e, $n) {
    if ($n <= 0 || $e < 0) {
        throw new InvalidArgumentException("expmod : n doit être positif et e doit être positif ou nul.");
    }

    $resultat = 1 % $n;
    $x        = (($x % $n) + $n) % $n;

    while ($e > 0) {
        if ($e % 2 == 1) {
            $resultat = ($resultat * $x) % $n;
        }
        $x = ($x * $x) % $n;
        $e = intdiv($e, 2);
    }

    return $resultat;
}

function gcd($a, $b) {
    $a = abs($a);
    $b = abs($b);

    while ($b != 0) {
        $reste = $a % $b;
        $a     = $b;
        $b     = $reste;
    }

    return $a;
}

function inversemod($e, $f) {
    if ($f <= 1) {
        return "Erreur : le modulo doit être supérieur à 1.";
    }

    $ancienReste       = $e;
    $reste             = $f;
    $ancienCoefficient = 1;
    $coefficient       = 0;

    while ($reste != 0) {
        $quotient = intdiv($ancienReste, $reste);

        $nouveauReste = $ancienReste - $quotient * $reste;
        $ancienReste  = $reste;
        $reste        = $nouveauReste;

        $nouveauCoefficient = $ancienCoefficient - $quotient * $coefficient;
        $ancienCoefficient  = $coefficient;
        $coefficient        = $nouveauCoefficient;
    }

    if ($ancienReste != 1) {
        return "Erreur : $e n'est pas inversible modulo $f (PGCD = $ancienReste).";
    }

    return (($ancienCoefficient % $f) + $f) % $f;
}

function RSAencrypt($x, $e, $n) {
    return expmod($x, $e, $n);
}

function RSAdecrypt($y, $e, $p, $q) {
    if ($p <= 1 || $q <= 1 || $p == $q) {
        return "Erreur : p et q doivent être deux entiers distincts supérieurs à 1.";
    }

    $n   = $p * $q;
    $phi = ($p - 1) * ($q - 1);
    $d   = inversemod($e, $phi);

    if (!is_int($d)) {
        return $d;
    }

    return expmod($y, $d, $n);
}

function nombreTemoinsFermat($n) {
    if ($n <= 2) {
        return 0;
    }

    $compteur = 0;

    for ($a = 2; $a <= $n - 1; $a++) {
        if (gcd($a, $n) == 1 && expmod($a, $n - 1, $n) != 1) {
            $compteur++;
        }
    }

    return $compteur;
}

function estTemoin($a, $d, $s, $n) {
    $x = expmod($a, $d, $n);

    if ($x == 1 || $x == $n - 1) {
        return false;
    }

    for ($r = 1; $r <= $s - 1; $r++) {
        $x = ($x * $x) % $n;

        if ($x == $n - 1) {
            return false;
        }
    }

    return true;
}

function millerRabin($n, $k) {
    if ($n < 2 || $k < 1) {
        return false;
    }

    if ($n == 2 || $n == 3) {
        return true;
    }

    if ($n % 2 == 0) {
        return false;
    }

    $d = $n - 1;
    $s = 0;

    while ($d % 2 == 0) {
        $d = intdiv($d, 2);
        $s++;
    }

    for ($i = 0; $i < $k; $i++) {
        $a = random_int(2, $n - 2);

        if (estTemoin($a, $d, $s, $n)) {
            return false;
        }
    }

    return true;
}

function premierAleatoire($min, $max) {
    if ($min > $max || $max < 3) {
        throw new InvalidArgumentException("Intervalle invalide pour rechercher un nombre premier.");
    }

    $premierImpair = max(3, $min);

    if ($premierImpair % 2 == 0) {
        $premierImpair++;
    }

    if ($premierImpair > $max) {
        throw new InvalidArgumentException("Aucun entier impair possible dans cet intervalle.");
    }

    while (true) {
        $candidat = random_int($premierImpair, $max);

        if ($candidat % 2 == 0) {
            $candidat++;
        }

        if ($candidat > $max) {
            continue;
        }

        if (millerRabin($candidat, 20)) {
            return $candidat;
        }
    }
}

function genererClesRSA($min, $max) {
    $p = premierAleatoire($min, $max);

    do {
        $q = premierAleatoire($min, $max);
    } while ($q == $p);

    $n   = $p * $q;
    $phi = ($p - 1) * ($q - 1);

    do {
        $e = random_int(3, $phi - 1);
        if ($e % 2 == 0) {
            $e++;
        }
    } while ($e >= $phi || gcd($e, $phi) != 1);

    $d = inversemod($e, $phi);

    return [
        'p' => $p,
        'q' => $q,
        'n' => $n,
        'phi' => $phi,
        'e' => $e,
        'd' => $d
    ];
}

titre("1.1 - Exponentiation rapide");
echo "expmod(2,30,9) = " . expmod(2, 30, 9) . " (attendu : 1)" . PHP_EOL;

titre("1.2 - PGCD / Euclide");
echo "gcd(12,26) = " . gcd(12, 26) . " (attendu : 2)" . PHP_EOL;

titre("1.3 - Inverse modulaire / Euclide étendu");
echo "inversemod(5,26) = " . inversemod(5, 26) . " (attendu : 21)" . PHP_EOL;
echo "inversemod(2,26) = " . inversemod(2, 26) . " (une erreur est attendue)" . PHP_EOL;

titre("1.4 - Chiffrement et déchiffrement RSA");
echo "RSAencrypt(3,5,85) = " . RSAencrypt(3, 5, 85) . " (attendu : 73)" . PHP_EOL;
echo "RSAdecrypt(2,5,5,17) = " . RSAdecrypt(2, 5, 5, 17) . " (attendu : 32)" . PHP_EOL;

titre("Q1(b) - Témoins de Fermat");
echo "nombreTemoinsFermat(561) = " . nombreTemoinsFermat(561) . " (attendu : 0)" . PHP_EOL;
echo "nombreTemoinsFermat(15) = " . nombreTemoinsFermat(15) . PHP_EOL;

titre("1.5 - Test de Miller-Rabin (k = 20)");
foreach ([561, 221, 997, 7919] as $nombre) {
    echo "millerRabin($nombre,20) = "
    . (millerRabin($nombre, 20) ? "probablement premier" : "composé")
    . PHP_EOL;
}

titre("1.5(d) - Génération d'une clé RSA dans [100,999]");
$cle = genererClesRSA(100, 999);

foreach ($cle as $nom => $valeur) {
    echo str_pad($nom, 5) . " = " . $valeur . PHP_EOL;
}

$message          = 42;
$messageChiffre   = RSAencrypt($message, $cle['e'], $cle['n']);
$messageDechiffre = expmod($messageChiffre, $cle['d'], $cle['n']);

echo "Vérification RSA : message $message -> chiffré $messageChiffre"
. " -> déchiffré $messageDechiffre" . PHP_EOL;


titre("Partie 2 - Exercice 1 : authentification");

$p  = 11;
$q  = 23;
$eB = 17;

$nB   = $p * $q;
$phiB = ($p - 1) * ($q - 1);
$dB   = inversemod($eB, $phiB);

echo "(a) p=11 et q=23 sont premiers et distincts : "
. (millerRabin($p, 10) && millerRabin($q, 10) && $p != $q ? "oui" : "non")
. PHP_EOL;

echo "(b) nB = $nB ; phi(nB) = $phiB" . PHP_EOL;

echo "(c) PGCD(17,220) = " . gcd($eB, $phiB)
. " : l'exposant public est admissible." . PHP_EOL;

echo "(d) dB = inversemod(17,220) = $dB" . PHP_EOL;

$r = 100;
$s = expmod($r, $dB, $nB);
echo "(e) Signature s = 100^13 mod 253 = $s" . PHP_EOL;

echo "(f) Alice vérifie la signature en calculant s^eB mod nB." . PHP_EOL;
echo "    Si Bob chiffre s avec la clé publique d'Alice, Alice déchiffre"
. " d'abord avec sa clé privée, puis vérifie avec la clé publique de Bob."
. PHP_EOL;

$verification = expmod($s, $eB, $nB);
echo "(g) s^17 mod 253 = $verification : "
. ($verification == $r ? "le défi est retrouvé." : "échec de vérification.")
. PHP_EOL;

$imposteur = expmod(200, $eB, $nB);
echo "(h) 200^17 mod 253 = $imposteur : "
. ($imposteur != $r ? "le défi 100 n'est pas retrouvé." : "le défi est retrouvé.")
. PHP_EOL;

titre("(i) Tableau récapitulatif");
printf("%-34s %-12s %s\n", "Grandeur", "Valeur", "Appel PHP");
printf("%-34s %-12s %s\n", "nB, phi(nB)", "$nB, $phiB", "11*23 ; (11-1)*(23-1)");
printf("%-34s %-12s %s\n", "dB = eB^-1 mod phi(nB)", $dB, "inversemod(17,220)");
printf("%-34s %-12s %s\n", "signature s = 100^dB mod nB", $s, "expmod(100,13,253)");
printf("%-34s %-12s %s\n", "verification s^eB mod nB", $verification, "expmod($s,17,253)");
printf("%-34s %-12s %s\n", "imposteur 200^eB mod nB", $imposteur, "expmod(200,17,253)");
