<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CookingSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CookingSession>
 *
 * @method CookingSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method CookingSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<CookingSession> findAll()
 * @method array<CookingSession> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CookingSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CookingSession::class);
    }

//    /**
//     * @return CookingSession[] Returns an array of CookingSession objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CookingSession
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
