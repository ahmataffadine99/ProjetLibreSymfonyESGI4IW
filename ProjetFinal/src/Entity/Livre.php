<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NEW

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre extends ObjetCollection
{
    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $auteur = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $isbn = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?int $nombrePages = null;

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombrePages;
    }

    public function setNombrePages(?int $nombrePages): static
    {
        $this->nombrePages = $nombrePages;

        return $this;
    }

     /**
      * pour le test unitaire 
     * Retourne une chaîne formatée de l'auteur et du titre.
     */
    public function getFormattedTitleAndAuthor(): string
    {
        return $this->getNom() . ' par ' . $this->getAuteur();
    }
}