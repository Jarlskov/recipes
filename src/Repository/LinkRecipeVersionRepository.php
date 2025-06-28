<?php

namespace App\Repository;

use App\Entity\LinkRecipeVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkRecipeVersion>
 *
 * @method LinkRecipeVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkRecipeVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkRecipeVersion[]    findAll()
 * @method LinkRecipeVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRecipeVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkRecipeVersion::class);
    }

    public function save(LinkRecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LinkRecipeVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 