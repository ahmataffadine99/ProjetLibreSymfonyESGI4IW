<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface as PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups; // NOUVEAU

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU : Exposez l'ID de l'utilisateur pour la lecture/écriture API
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['collection_read', 'collection_write'])] // NOUVEAU : Exposez l'email pour la lecture/écriture API
    private ?string $email = null;

    #[ORM\Column]
    // PAS DE GROUPE ICI : Les rôles ne sont généralement pas exposés directement via l'API publique
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    // AUCUN GROUPE ICI : NE JAMAIS EXPOSER LE MOT DE PASSE EN API
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: ObjetCollection::class)]
    // PAS DE GROUPE ICI pour éviter les boucles infinies.
    private Collection $objetsAjoutes;

    public function __construct()
    {
        $this->objetsAjoutes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getSalt(): ?string
    {
        // Not needed when using modern algorithms like bcrypt or sodium
        return null;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, ObjetCollection>
     */
    public function getObjetsAjoutes(): Collection
    {
        return $this->objetsAjoutes;
    }

    public function addObjetsAjoute(ObjetCollection $objetsAjoute): static
    {
        if (!$this->objetsAjoutes->contains($objetsAjoute)) {
            $this->objetsAjoutes->add($objetsAjoute);
            $objetsAjoute->setUtilisateur($this);
        }

        return $this;
    }

    public function removeObjetsAjoute(ObjetCollection $objetsAjoute): static
    {
        if ($this->objetsAjoutes->removeElement($objetsAjoute)) {
            // set the owning side to null (unless already changed)
            if ($objetsAjoute->getUtilisateur() === $this) {
                $objetsAjoute->setUtilisateur(null);
            }
        }

        return $this;
    }
}