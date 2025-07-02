<?php
// src/Repository/ObjetCollectionRepository.php

namespace App\Repository;

use App\Entity\ObjetCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Livre;
use App\Entity\Vinyle;
use App\Entity\JeuVideo;
use App\Entity\Utilisateur;

/**
 * @extends ServiceEntityRepository<ObjetCollection>
 */
class ObjetCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjetCollection::class);
    }

    /**
     * Récupère tous les objets pour un utilisateur donné.
     * @return ObjetCollection[]
     */
    public function findByUser(Utilisateur $utilisateur): array
    {
        return $this->createQueryBuilder('oc')
            ->andWhere('oc.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->orderBy('oc.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les objets d'un type spécifique pour un utilisateur donné.
     * @return ObjetCollection[]
     */
    public function findByUserAndType(Utilisateur $utilisateur, string $type): array
    {
        $qb = $this->createQueryBuilder('oc')
            ->andWhere('oc.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur);

        $entityClass = $this->getClassNameForType($type);
        if ($entityClass !== ObjetCollection::class) {
            $qb->andWhere('oc INSTANCE OF ' . $entityClass);
        }

        return $qb
            ->orderBy('oc.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les objets d'un type spécifique pour TOUS les utilisateurs.
     * C'est la méthode à utiliser pour le filtrage de la page /toutes-les-collections
     * quand un type spécifique est sélectionné.
     * @return ObjetCollection[]
     */
    public function findByType(string $type): array
    {
        $qb = $this->createQueryBuilder('oc')
                    ->leftJoin('oc.utilisateur', 'u') // Jointure pour afficher le propriétaire
                    ->addSelect('u');

        $entityClass = $this->getClassNameForType($type);
        if ($entityClass !== ObjetCollection::class) {
            $qb->andWhere('oc INSTANCE OF ' . $entityClass);
        }

        return $qb
            ->orderBy('oc.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Helper method to get the FQCN for a given type string.
     */
    private function getClassNameForType(string $type): string
    {
        return match ($type) {
            'livre' => Livre::class,
            'vinyle' => Vinyle::class,
            'jeu-video' => JeuVideo::class,
            default => ObjetCollection::class, // Retourne la classe de base si le type est inconnu ou vide
        };
    }

    /**
     * Récupère tous les objets de collection avec leur utilisateur associé.
     * Idéal pour la page "Toutes les collections" sans filtre.
     * @return ObjetCollection[]
     */
    public function findAllObjectsWithUser(): array
    {
        return $this->createQueryBuilder('oc')
            ->leftJoin('oc.utilisateur', 'u')
            ->addSelect('u') // Pour charger l'utilisateur et éviter les requêtes N+1 dans Twig
            ->orderBy('oc.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }
}