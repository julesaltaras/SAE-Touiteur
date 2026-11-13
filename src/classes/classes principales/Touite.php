<?php
namespace iutnc\touiteur\model;

class Touite
{
    public int $id;
    public string $contenu;
    public string $date;
    public Utilisateur $auteur;
    public int $score;
    public array $tags;
    public ?array $image;

    public function __construct(int $id, string $contenu, string $date, Utilisateur $auteur, int $score, array $tags, ?array $image)
    {
        $this->id = $id;
        $this->contenu = $contenu;
        $this->date = $date;
        $this->auteur = $auteur;
        $this->score = $score;
        $this->tags = $tags;
        $this->image = $image;
    }
    public static function verifierContenu(string $texte): void
    {
        if (trim($texte) === '') {
            throw new \DomainException('Le touite est vide.');
        }

        if (mb_strlen($texte, 'UTF-8') > 235) {
            throw new \DomainException('235 caractères maximum.');
        }
    }

    public static function extraireTags(string $texte): array
    {
        $tags = [];
        $texte = str_replace(["\n", "\r", "\t"], ' ', $texte);
        $mots = explode(' ', $texte);

        foreach ($mots as $mot) {
            $mot = trim($mot, ".,!?;:()\"'");

            if ($mot !== '' && $mot[0] === '#') {
                $tag = substr($mot, 1);
                $tag = mb_strtolower($tag, 'UTF-8');

                if (mb_strlen($tag, 'UTF-8') > 15) {
                    throw new \DomainException('Tag trop long : 15 caractères maximum.');
                }

                if ($tag !== '' && !in_array($tag, $tags)) {
                    $tags[] = $tag;
                }
            }
        }

        return $tags;
    }
}