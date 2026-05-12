<?php

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discussion>
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    /**
     * @return Discussion[]
     */
    public function findForParticipant(User $user): array
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.discuss', 'participant')
            ->andWhere('participant = :user')
            ->setParameter('user', $user)
            ->orderBy('d.updated_at', 'DESC')
            ->addOrderBy('d.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
