<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

     /**
     * Recherche + tri + pagination
     */
    public function searchPaginated(?string $q, string $sort, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($q) {
            $qb
                ->andWhere('c.nom LIKE :q OR c.prenom LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        // tri
        switch ($sort) {
            case 'name_asc':
                $qb->addOrderBy('c.nom', 'ASC');
                break;
            case 'name_desc':
                $qb->addOrderBy('c.nom', 'DESC');
                break;
            default:
                $qb->addOrderBy('c.id', 'DESC');
        }

        // total pour pagination
        $total = (clone $qb)
            ->select('COUNT(c.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $clients = $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [$clients, $total];
    }
}


    
