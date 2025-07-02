<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NEW

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $nom = null;

    /**
     * @var Collection<int, ObjetCollection>
     */
    #[ORM\ManyToMany(mappedBy: 'tags', targetEntity: ObjetCollection::class)]
    // NO GROUP HERE to avoid infinite loops.
    private Collection $objetsCollection;

    public function __construct()
    {
        $this->objetsCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, ObjetCollection>
     */
    public function getObjetsCollection(): Collection
    {
        return $this->objetsCollection;
    }

    public function addObjetsCollection(ObjetCollection $objetsCollection): static
    {
        if (!$this->objetsCollection->contains($objetsCollection)) {
            $this->objetsCollection->add($objetsCollection);
        }

        return $this;
    }

    public function removeObjetsCollection(ObjetCollection $objetsCollection): static
    {
        $this->objetsCollection->removeElement($objetsCollection);

        return $this;
    }
}