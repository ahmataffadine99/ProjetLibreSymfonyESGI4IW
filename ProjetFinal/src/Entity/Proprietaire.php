<?php

namespace App\Entity;

use App\Repository\ProprietaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProprietaireRepository::class)]
class Proprietaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, ObjetCollection>
     */
    #[ORM\OneToMany(targetEntity: ObjetCollection::class, mappedBy: 'proprietaire')]
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
            $objetsCollection->setProprietaire($this);
        }

        return $this;
    }

    public function removeObjetsCollection(ObjetCollection $objetsCollection): static
    {
        if ($this->objetsCollection->removeElement($objetsCollection)) {
            // set the owning side to null (unless already changed)
            if ($objetsCollection->getProprietaire() === $this) {
                $objetsCollection->setProprietaire(null);
            }
        }

        return $this;
    }
}
