<?php

namespace App\Repository;

use App\Entity\WebhookSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WebhookSchedule>
 */
class WebhookScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebhookSchedule::class);
    }

    public function findDueWebhooks(\DateTimeInterface $now): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.enabled = true')
            ->andWhere('w.nextRunAt <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return WebhookSchedule[] Returns an array of WebhookSchedule objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WebhookSchedule
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
