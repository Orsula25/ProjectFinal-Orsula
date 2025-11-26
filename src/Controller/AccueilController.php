<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VenteRepository;
use App\Repository\AchatRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;

final class AccueilController extends AbstractController
{

    #[Route('/accueil', name: 'app_accueil')]
    public function index(
        VenteRepository $venteRepo,
        AchatRepository $achatRepo,
        ProduitRepository $produitRepo
    ): Response
    {
        // Chiffres globaux
        $chiffreAffaire = $venteRepo->getChiffreAffaire();
        $valeurStock    = $produitRepo->getValeurStock();
        $totalAchats    = $achatRepo->getTotalAchats();

        // Produits sous seuil / rupture
        $nbSousSeuil = $produitRepo->getProduitSousSeuil();
        $nbRupture   = $produitRepo->countProduitsEnRupture();

        // Total produits
        $totalProduits = $produitRepo->count([]);

        // Produits OK
        $nbOk = $totalProduits - $nbSousSeuil - $nbRupture;

        $nbOk = $totalProduits - $nbSousSeuil - $nbRupture;
        

        return $this->render('accueil/index.html.twig', [
            'chiffreAffaire' => $chiffreAffaire,
            'totalAchats'    => $totalAchats,
            'valeurStock'    => $valeurStock,
            'nbSousSeuil'    => $nbSousSeuil,
            'nbRupture'      => $nbRupture,
            'nbOk'           => $nbOk,
        ]);
    }


    #[Route('/accueil/index.html.twig')]
    public function testModele(EntityManagerInterface $en)
    {
        $rep = $en->getRepository(Produit::class);
        $arrayProduits = $rep->findAll();

        return $this->render('accueil/test_Modele.html.twig', [
            'produits' => $arrayProduits
        ]);
    }

}
