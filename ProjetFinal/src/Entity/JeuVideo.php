<?php

namespace App\Entity;

use App\Repository\JeuVideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JeuVideoRepository::class)]
class JeuVideo extends ObjetCollection
{
   

    #[ORM\Column(length: 255)]
    private ?string $studio = null;

    #[ORM\Column(length: 100)]
    private ?string $plateforme = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $classification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudio(): ?string
    {
        return $this->studio;
    }

    public function setStudio(string $studio): static
    {
        $this->studio = $studio;

        return $this;
    }

    public function getPlateforme(): ?string
    {
        return $this->plateforme;
    }

    public function setPlateforme(string $plateforme): static
    {
        $this->plateforme = $plateforme;

        return $this;
    }

    public function getClassification(): ?string
    {
        return $this->classification;
    }

    public function setClassification(?string $classification): static
    {
        $this->classification = $classification;

        return $this;
    }
}
