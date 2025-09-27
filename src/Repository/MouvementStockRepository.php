<?php

namespace App\Repository;

use App\Entity\MouvementStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvementStock>
 */
class MouvementStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementStock::class);
    }

    //    /**
    //     * @return MouvementStock[] Returns an array of MouvementStock objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MouvementStock
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function enregistrerMouvementStock (Produit $produit, int $quantite, typeMouvement $type):void
    {
       $m = new MouvementStock();
       $m->setProduit($produit);
       $m->setQuantite($quantite);
       $m->setTypeMouvement($type);
       $m->setDateMouvement(new \DateTimeImmutable());
       $m->setDateModification(new \DateTimeImmutable());

       $this -> _em->persist($m);

       // mise à jour du stock produit 
       if ($type === typeMouvement::ENTREE) {
           $produit->setQuantiteStock($produit->getQuantiteStock() + $quantite);
       }elseif ($type === typeMouvement::SORTIE) {
           $produit->setQuantiteStock(max(0, $produit->getQuantiteStock()-$quantite));
           $this->_em->flush();
       }
    }
}
