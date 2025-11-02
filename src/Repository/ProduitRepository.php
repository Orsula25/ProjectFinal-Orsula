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

    //Recherche les produits par réference ou par nom 
    // $term : ce que l'utilisateur tape dans la barre de recherche 
    // $ limit : combien d'élement on veut 
    // $offset : à partir de quel enregistrement on commence 
    public function searchByNameOrReference(string $term, int $limit, int $offset): array {
        return $this->createQueryBuilder('p')
        // on cherche les produits par reference ou par nom 
        ->andWhere('p.reference LIKE :term OR p.nom LIKE :term')
        ->setParameter('term', '%'.$term.'%')

        // on trie pour avoir le dernier en premier 
        ->orderBy('p.id', 'DESC')
        // pagination 
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }

    // mm  rechherche que ci-dessus mais on compte juste le nbr de résultats. (combien par page ) 

    public function countSearch(string $term): int {
        return (int) $this->createQueryBuilder('p')
       ->select('COUNT(p.id)')
       ->andWhere('p.reference LIKE :term OR p.nom LIKE :term')
       ->setParameter('term', '%'.$term.'%')
       ->getQuery()
       ->getSingleScalarResult();
    }


    public function findBySearch(?string $q, string $sort, int $limit, int $offset): array
{
    $qb = $this->createQueryBuilder('p');

    if ($q) {
        $qb->andWhere('p.nom LIKE :q OR p.description LIKE :q')
           ->setParameter('q', '%'.$q.'%');
    }

    // tri
    switch ($sort) {
        case 'nom_asc':
            $qb->addOrderBy('p.nom', 'ASC');
            break;
        case 'nom_desc':
            $qb->addOrderBy('p.nom', 'DESC');
            break;
        default:
            $qb->addOrderBy('p.id', 'DESC'); // ou dateCreation
    }

    return $qb
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult()
    ;
}



}