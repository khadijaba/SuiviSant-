<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Find the latest questions with pagination
     */
    public function findLatest(int $page = 1, int $limit = 10, ?string $category = null): Paginator
    {
        $query = $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        if ($category) {
            $query->andWhere('q.category = :category')
                ->setParameter('category', $category);
        }

        return new Paginator($query);
    }

    /**
     * Find popular questions based on views and answer count
     */
    public function findPopular(int $limit = 5): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('q.views', 'DESC')
            ->addOrderBy('SIZE(q.answers)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find unanswered questions
     */
    public function findUnanswered(int $limit = 10): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->setParameter('status', 'active')
            ->andWhere('SIZE(q.answers) = 0')
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Search questions by title or content
     */
    public function searchQuestions(string $query): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->andWhere('q.title LIKE :query OR q.content LIKE :query')
            ->setParameter('status', 'active')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get question categories with count
     */
    public function findCategories(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT category, COUNT(id) as count
            FROM question
            WHERE category IS NOT NULL
            GROUP BY category
            ORDER BY count DESC
        ';
        
        $stmt = $conn->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }

    /**
     * Find questions that have at least one answer
     */
    public function findQuestionsWithAnswers(int $limit = 20): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->setParameter('status', 'active')
            ->andWhere('SIZE(q.answers) > 0')
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 