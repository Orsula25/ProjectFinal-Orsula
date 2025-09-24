<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VenteRepository;
use App\Repository\AchatRepository;
use App\Repository\ProduitRepository;
// importer les entité 
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
        $chiffreAffaire= $venteRepo->getChiffreAffaire();
        $valeurStock = $produitRepo->getValeurStock();
        $totalAchats = $achatRepo->getTotalAchats();
        $nbSousSeuil = $produitRepo->getProduitSousSeuil();
        $nbRuptures = $produitRepo->countProduitsEnRupture();
     
        
        
        return $this->render('accueil/index.html.twig', [
            'chiffreAffaire' => $chiffreAffaire,
            'totalAchats' => $totalAchats,
            'valeurStock' => $valeurStock,
            'nbSousSeuil' => $nbSousSeuil,
            'nbRuptures' => $nbRuptures,
        ]);
    }

    #[Route('/accueil/index.html.twig')]
    public function testModele(EntityManagerInterface $en){
        // on  va obtenir des entité de la bd
        //1. obtenir le repo de lentité 
        $rep = $en->getRepository(Produit::class);
       $arrayProduits = $rep->findAll();
       

       $vars = [
        'produits' => $arrayProduits
       ];

       return $this->render('accueil/test_Modele.html.twig', $vars);
    }




    

}
