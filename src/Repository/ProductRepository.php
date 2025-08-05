<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllSorted(): array
    {
        $qb = $this->createQueryBuilder('o');
        $qb->orderBy('o.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
