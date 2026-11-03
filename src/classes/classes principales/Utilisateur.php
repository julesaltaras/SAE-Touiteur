<?php
namespace iutnc\touiteur\model;

class Utilisateur
{
    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public int $nbTouites;

    public function __construct(int $id, string $nom, string $prenom, string $email, int $nbTouites)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->nbTouites = $nbTouites;
    }

    public function nomComplet(): string
    {
        return $this->prenom . " " . $this->nom;
    }
}
