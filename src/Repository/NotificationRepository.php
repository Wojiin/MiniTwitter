<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @return Notification[]
     */
    public function findForRecipient(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.message_notif', 'm')
            ->leftJoin('n.post_notif', 'p')
            ->leftJoin('n.reply_notif', 'r')
            ->leftJoin('n.discussion_notif', 'd')
            ->addSelect('m', 'p', 'r', 'd')
            ->andWhere('n.receive = :user')
            ->setParameter('user', $user)
            ->orderBy('n.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
