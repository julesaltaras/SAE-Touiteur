<?php
namespace iutnc\touiteur\model;

class Tag
{
    public int $id;
    public string $libelle;
    public string $description;

    public function __construct(int $id, string $libelle, string $description)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        $this->description = $description;
    }
}
