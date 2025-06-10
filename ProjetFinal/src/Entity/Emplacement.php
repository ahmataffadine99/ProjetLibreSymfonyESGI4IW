<?php

namespace App\Entity;

use App\Repository\EmplacementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NOUVEAU

#[ORM\Entity(repositoryClass: EmplacementRepository::class)]
class Emplacement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?string $nom = null;

    /**
     * @var Collection<int, ObjetCollection>
     */
    #[ORM\OneToMany(targetEntity: ObjetCollection::class, mappedBy: 'emplacement')]
    // PAS DE GROUPE ICI pour éviter les boucles infinies.
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
            $objetsCollection->setEmplacement($this);
        }

        return $this;
    }

    public function removeObjetsCollection(ObjetCollection $objetsCollection): static
    {
        if ($this->objetsCollection->removeElement($objetsCollection)) {
            // set the owning side to null (unless already changed)
            if ($objetsCollection->getEmplacement() === $this) {
                $objetsCollection->setEmplacement(null);
            }
        }

        return $this;
    }
}