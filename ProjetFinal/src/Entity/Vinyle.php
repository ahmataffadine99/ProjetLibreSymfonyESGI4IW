<?php

namespace App\Entity;

use App\Repository\VinyleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NEW

#[ORM\Entity(repositoryClass: VinyleRepository::class)]
class Vinyle extends ObjetCollection
{
    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $artiste = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $titreAlbum = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $genre = null;

    public function getArtiste(): ?string
    {
        return $this->artiste;
    }

    public function setArtiste(string $artiste): static
    {
        $this->artiste = $artiste;

        return $this;
    }

    public function getTitreAlbum(): ?string
    {
        return $this->titreAlbum;
    }

    public function setTitreAlbum(string $titreAlbum): static
    {
        $this->titreAlbum = $titreAlbum;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }
}