<?php

namespace App\Repository;

use App\Entity\FullRecipeVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FullRecipeVersion>
 *
 * @method FullRecipeVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FullRecipeVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FullRecipeVersion[]    findAll()
 * @method FullRecipeVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FullRecipeVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FullRecipeVersion::class);
    }

    public function save(FullRecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FullRecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 