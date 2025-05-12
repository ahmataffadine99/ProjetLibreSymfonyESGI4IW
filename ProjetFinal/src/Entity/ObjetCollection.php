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

#[ORM\Entity(repositoryClass: ObjetCollectionRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['livre' => 'Livre', 'vinyle' => 'Vinyle', 'jeu_video' => 'JeuVideo'])]
abstract class ObjetCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAjout = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'objets')]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    private ?Proprietaire $proprietaire = null;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    private ?Emplacement $emplacement = null;


    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatutObjet $statut = null;

    #[ORM\ManyToOne(inversedBy: 'objetsCollection')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;



/**
 * @var Collection<int, Tag>
 */
#[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'objetsCollection')]
#[ORM\JoinTable(name: 'objet_collection_tag')]
#[ORM\JoinColumn(name: 'objet_collection_id', referencedColumnName: 'id')]
#[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
private Collection $tags;


    
    #[ORM\ManyToOne(inversedBy: 'objetsAjoutes')]
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
            $tag->addObjetsCollection($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeObjetsCollection($this);
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

    /**
     * @return Collection<int, Categorie>
     */


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
            $category->addObjet($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeObjet($this);
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