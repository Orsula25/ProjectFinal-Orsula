<?php

namespace App\Repository;

use App\Entity\Fournisseur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fournisseur>
 */
class FournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fournisseur::class);
    }

    //    /**
    //     * @return Fournisseur[] Returns an array of Fournisseur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Fournisseur
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function searchPaginated(?string $q, string $sort, int $limit, int $offset): array
{
    $qb = $this->createQueryBuilder('f');

    // recherche
    if ($q) {
        $qb
            ->andWhere(
                'f.nom LIKE :q
                 OR f.email LIKE :q
                 OR f.telephone LIKE :q
                 OR f.adresse LIKE :q
                 OR f.numTva LIKE :q'
            )
            ->setParameter('q', '%'.$q.'%');
    }

    // tri
    switch ($sort) {
        case 'name_asc':
            $qb->addOrderBy('f.nom', 'ASC');
            break;
        case 'name_desc':
            $qb->addOrderBy('f.nom', 'DESC');
            break;
        default:
            $qb->addOrderBy('f.id', 'DESC');
            break;
    }

    // total pour pagination
    $total = (clone $qb)
        ->select('COUNT(f.id)')
        ->resetDQLPart('orderBy')
        ->getQuery()
        ->getSingleScalarResult();

    // résultats paginés
    $fournisseurs = $qb
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    return [$fournisseurs, $total];
}


}
