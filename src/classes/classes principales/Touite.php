<?php
namespace iutnc\touiteur\model;

class Touite {
    public int $id;
    public string $contenu;
    public string $date;
    public Utilisateur $auteur;
    public int $score;
    public array $tags;
    public ?array $image;

    public function __construct(int $id, string $contenu, string $date, Utilisateur $auteur, int $score, array $tags, ?array $image) {
        $this->id      = $id;
        $this->contenu = $contenu;
        $this->date    = $date;
        $this->auteur  = $auteur;
        $this->score   = $score;
        $this->tags    = $tags;
        $this->image   = $image;
    }
}