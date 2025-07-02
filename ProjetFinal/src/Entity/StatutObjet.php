<?php

namespace App\Entity;

use App\Repository\StatutObjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NEW

#[ORM\Entity(repositoryClass: StatutObjetRepository::class)]
class StatutObjet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['collection_read', 'collection_write'])] // NEW
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'statut', targetEntity: ObjetCollection::class)]
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
            $objetsCollection->setStatut($this);
        }

        return $this;
    }

    public function removeObjetsCollection(ObjetCollection $objetsCollection): static
    {
        if ($this->objetsCollection->removeElement($objetsCollection)) {
            // set the owning side to null (unless already changed)
            if ($objetsCollection->getStatut() === $this) {
                $objetsCollection->setStatut(null);
            }
        }

        return $this;
    }
}