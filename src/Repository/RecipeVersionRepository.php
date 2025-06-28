<?php

namespace App\Repository;

use App\Entity\RecipeVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecipeVersion>
 *
 * @method RecipeVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeVersion[]    findAll()
 * @method RecipeVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeVersion::class);
    }

    public function save(RecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 