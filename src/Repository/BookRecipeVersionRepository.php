<?php

namespace App\Repository;

use App\Entity\BookRecipeVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookRecipeVersion>
 *
 * @method BookRecipeVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookRecipeVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookRecipeVersion[]    findAll()
 * @method BookRecipeVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRecipeVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookRecipeVersion::class);
    }

    public function save(BookRecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookRecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 