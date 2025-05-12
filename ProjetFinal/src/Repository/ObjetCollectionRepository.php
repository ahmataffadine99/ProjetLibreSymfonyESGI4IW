<?php

namespace App\Repository;

use App\Entity\ObjetCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Livre;
use App\Entity\Vinyle;
use App\Entity\JeuVideo;
/**
 * @extends ServiceEntityRepository<ObjetCollection>
 */
class ObjetCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjetCollection::class);
    }

    //    /**
    //     * @return ObjetCollection[] Returns an array of ObjetCollection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ObjetCollection
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('oc')
            ->where('oc INSTANCE OF :entity')
            ->setParameter('entity', $this->getClassNameForType($type))
            ->getQuery()
            ->getResult()
        ;
    }
    
    private function getClassNameForType(string $type): string
    {
        return match ($type) {
            'livre' => Livre::class,
            'vinyle' => Vinyle::class,
            'jeu-video' => JeuVideo::class,
            default => ObjetCollection::class, // Utilisez ObjetCollection par défaut pour éviter les erreurs
        };
    }
}
