<?php

namespace App\Entity;
use App\Entity\Tag;
use App\Entity\Emplacement;
use App\Repository\ObjetCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Categorie;
use Symfony\Component\Serializer\Annotation\Groups; // NOUVEAU

#[ORM\Entity(repositoryClass: ObjetCollectionRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['livre' => 'Livre', 'vinyle' => 'Vinyle', 'jeu_video' => 'JeuVideo'])]
abstract class ObjetCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?\DateTimeInterface $dateAjout = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?string $description = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'objets')]
    // PAS DE GROUPE ICI pour éviter les boucles infinies.
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    // À ajuster si vous voulez exposer le propriétaire dans l'API. Si oui, ajoutez un groupe, sinon, pas de groupe.
    private ?Proprietaire $proprietaire = null;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?Emplacement $emplacement = null;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?StatutObjet $statut = null;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'objetsCollection')]
    #[ORM\JoinTable(name: 'objet_collection_tag')]
    #[ORM\JoinColumn(name: 'objet_collection_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'objetsAjoutes')]
    #[Groups(['collection_read'])] // NOUVEAU (lecture seule pour l'utilisateur lié)
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection(); // NOUVEAU : Initialiser la collection categories
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            // $tag->addObjetsCollection($this); // Cette ligne pourrait causer une boucle si non gérée côté Tag
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            // $tag->removeObjetsCollection($this); // Cette ligne pourrait causer une boucle si non gérée côté Tag
        }

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
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

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getStatut(): ?StatutObjet
    {
        return $this->statut;
    }

    public function setStatut(?StatutObjet $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            // $category->addObjet($this); // Cette ligne pourrait causer une boucle si non gérée côté Categorie
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            // $category->removeObjet($this); // Cette ligne pourrait causer une boucle si non gérée côté Categorie
        }

        return $this;
    }

    public function getProprietaire(): ?Proprietaire
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Proprietaire $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getEmplacement(): ?Emplacement
    {
        return $this->emplacement;
    }

    public function setEmplacement(?Emplacement $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}