<?php

namespace App\Repository;

use App\Entity\Achat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Achat>
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
        
    }

    //    /**
    //     * @return Achat[] Returns an array of Achat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Achat
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getTotalAchats():float
    {
        return(float)$this->createQueryBuilder('a')
        ->select('SUM(a.montantTotal)')
        ->getQuery()
        ->getSingleScalarResult();
    }
// cette méthode permet de trouver les clients par nom
    public function findClientsByNom(string $nom, ManagerRegistry $doctrine){
        $em = $doctrine->getManager(); 
        $query = $em->createQuery("SELECT client FROM App\Entity\Client where client.nom = :nom");
        $query->setParameter('nom', $nom);
        $clients = $query->getResult();
        dd($clients);
        
   
        

    }
}
