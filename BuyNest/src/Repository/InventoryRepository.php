<?php

namespace App\Repository;

use App\Entity\Inventory;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inventory>
 */
class InventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory::class);
    }

    public function getQuantitiesByProduct(): array
    {
        $results = $this->createQueryBuilder('i')
            ->select('IDENTITY(i.product) as productId, SUM(i.quantity) as totalQuantity')
            ->groupBy('i.product')
            ->getQuery()
            ->getResult();

        $quantities = [];
        foreach ($results as $row) {
            $quantities[$row['productId']] = (int) $row['totalQuantity'];
        }

        return $quantities;
    }

    public function findByProduct(Product $product): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Inventory[] Returns an array of Inventory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Inventory
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
