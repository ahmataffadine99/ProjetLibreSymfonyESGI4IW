<?php

namespace App\Entity;

use App\Repository\JeuVideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NOUVEAU

#[ORM\Entity(repositoryClass: JeuVideoRepository::class)]
class JeuVideo extends ObjetCollection
{
    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?string $studio = null;

    #[ORM\Column(length: 100)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?string $plateforme = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?string $classification = null;

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