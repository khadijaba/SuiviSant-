<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Answer>
 *
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * Find answers for a question, ordered by most voted
     */
    public function findAnswersForQuestion(Question $question): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.question = :question')
            ->setParameter('question', $question)
            ->orderBy('a.isAccepted', 'DESC')
            ->addOrderBy('a.votes', 'DESC')
            ->addOrderBy('a.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find the most helpful answers (highest votes)
     */
    public function findMostHelpful(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.votes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all answers by a specific author
     */
    public function findByAuthor(string $authorName): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.authorName = :authorName')
            ->setParameter('authorName', $authorName)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find accepted answers
     */
    public function findAcceptedAnswers(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isAccepted = :isAccepted')
            ->setParameter('isAccepted', true)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 