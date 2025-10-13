<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FoodItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FoodItem>
 *
 * @method FoodItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodItem[]    findAll()
 * @method FoodItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodItem::class);
    }

    public function save(FoodItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FoodItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find food items by name containing the given query
     *
     * @return FoodItem[]
     */
    public function findByNameContaining(string $query, int $limit = 10): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('f.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
