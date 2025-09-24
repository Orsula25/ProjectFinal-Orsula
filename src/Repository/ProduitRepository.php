<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // la valer total du stock 

    public function getValeurStock():float{
        return (float) $this ->createQueryBuilder('p')
        ->select('COALESCE(SUM(p.quantiteStock * p.prixUnitaire),0)')
        ->getQuery()
        ->getSingleScalarResult();
    }

    // les produit sous le seuil
    public function getProduitSousSeuil():int
    {

        return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.stockMin is not NULL')
        ->andWhere('p.quantiteStock < p.stockMin')
        ->getQuery()
        ->getSingleScalarResult();
      
    }


    // les produit en reprure de stock 

    public function countProduitsEnRupture():int {
        return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.quantiteStock = 0')
        ->getQuery()
        ->getSingleScalarResult();
    }

}