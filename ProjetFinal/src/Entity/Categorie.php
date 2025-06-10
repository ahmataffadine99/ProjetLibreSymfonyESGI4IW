<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; // NOUVEAU : Importez cette annotation

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU : Ajoutez les groupes ici
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU : Ajoutez les groupes ici
    private ?string $nom = null;

    /**
     * @var Collection<int, ObjetCollection>
     */
    #[ORM\ManyToMany(targetEntity: ObjetCollection::class, inversedBy: 'categories')]
    // NE PAS AJOUTER DE GROUPE ICI pour éviter les boucles infinies.
    // L'ObjetCollection sera sérialisé et aura une référence à la Catégorie,
    // mais la Catégorie ne sérialisera pas tous ses ObjetCollection liés pour éviter les boucles.
    private Collection $objets;

    public function __construct()
    {
        $this->objets = new ArrayCollection();
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
    public function getObjets(): Collection
    {
        return $this->objets;
    }

    public function addObjet(ObjetCollection $objet): static
    {
        if (!$this->objets->contains($objet)) {
            $this->objets->add($objet);
        }

        return $this;
    }

    public function removeObjet(ObjetCollection $objet): static
    {
        $this->objets->removeElement($objet);

        return $this;
    }
}