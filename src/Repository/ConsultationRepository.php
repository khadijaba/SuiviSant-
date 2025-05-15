<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Consultation>
 */
class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function findByUser($user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateConsultation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentByUser(Utilisateur $user, int $limit = 5)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateConsultation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countByUser(Utilisateur $user): int
    {
        return $this->count(['utilisateur' => $user]);
    }

    public function countByUserAndDateRange(Utilisateur $user, \DateTime $start, \DateTime $end): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.utilisateur = :user')
            ->andWhere('c.dateConsultation BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUpcomingByUser(Utilisateur $user, \DateTime $now): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.utilisateur = :user')
            ->andWhere('c.dateConsultation > :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getConsultationsParMois(Utilisateur $user): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT MONTH(c.date_consultation) as mois, COUNT(c.id) as total
            FROM consultation c
            WHERE c.utilisateur_id = :user_id
            AND c.date_consultation >= :year_start
            GROUP BY mois
            ORDER BY mois ASC
        ';
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'user_id' => $user->getId(),
            'year_start' => date('Y-01-01')
        ]);
        
        return $result->fetchAllAssociative();
    }
}
