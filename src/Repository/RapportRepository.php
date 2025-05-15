<?php

namespace App\Repository;

use App\Entity\Rapport;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rapport>
 */
class RapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapport::class);
    }

    //    /**
    //     * @return Rapport[] Returns an array of Rapport objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rapport
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countByUser(Utilisateur $user): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.consultation', 'c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByUser(Utilisateur $user)
    {
        return $this->createQueryBuilder('r')
            ->join('r.consultation', 'c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentByUser(Utilisateur $user, int $limit = 5)
    {
        return $this->createQueryBuilder('r')
            ->join('r.consultation', 'c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('r.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
