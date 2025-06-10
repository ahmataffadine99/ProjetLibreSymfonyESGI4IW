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

        // --- C'EST LA CORRECTION CRUCIALE ET DÉFINITIVE ---
        $entityClass = $this->getClassNameForType($type);
        if ($entityClass !== ObjetCollection::class) {
            // CONSTRUIRE LA CHAÎNE DIRECTEMENT SANS PARAMÈTRE POUR INSTANCE OF
            $qb->andWhere('oc INSTANCE OF ' . $entityClass);
        }
        // --------------------------------------------------
        // Ancien code (qui posait problème)
// $qb->andWhere('oc INSTANCE OF :entityClass')
//    ->setParameter('entityClass', $entityClass);

        return $qb
            ->orderBy('oc.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Gardez votre méthode findByType existante si elle est utilisée ailleurs (par exemple, par l'admin)
    // Note: Cette méthode pourrait aussi être améliorée avec la même logique si elle a le même problème.
    public function findByType(string $type): array
    {
        $qb = $this->createQueryBuilder('oc');
        $entityClass = $this->getClassNameForType($type);

        if ($entityClass !== ObjetCollection::class) {
             $qb->where('oc INSTANCE OF ' . $entityClass);
        }
        // Si $entityClass est ObjetCollection::class, on ne met pas de where clause spécifique
        // car cela signifierait "tous les objets", ce que le query builder fait déjà par défaut sans where.

        return $qb
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
            default => ObjetCollection::class,
        };
    }


 // --- C'EST CETTE MÉTHODE QUI DOIT ÊTRE PRÉSENTE ET CORRECTEMENT ÉCRITE ---
    /**
     * @return ObjetCollection[] Returns an array of ObjetCollection objects with their associated User.
     */
    public function findAllObjectsWithUser(): array
    {
        return $this->createQueryBuilder('oc')
            ->leftJoin('oc.utilisateur', 'u') // Jointure avec l'entité Utilisateur
            ->addSelect('u') // Sélectionne également les données de l'utilisateur
            ->orderBy('oc.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }

}





