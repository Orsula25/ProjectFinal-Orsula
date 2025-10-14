<?php
namespace App\Service;

use App\Entity\Produit;





// service de reapprovisionnement(calcule de la quantité à reapprovisionner)
class ReapprovisionnementService
{
    public function quantiteProposee(Produit $produit):int
    {
        $stock = (int) $produit->getQuantiteStock();
        $stockMin = $produit->getStockMin();

       
        // Si pas de stockMin défini, on commande au moins 1 (à toi de choisir la stratégie)
        if ($stockMin === null) {
            return 1;
        }

        // Cible = 3 × stockMin
        $cible = 3* (int) $stockMin;

        // Quantité à commander pour atteindre la cible
        $qte = $cible - $stock;

        // Toujours >= 1
        return $qte > 0 ? $qte : 1;
    }
}
