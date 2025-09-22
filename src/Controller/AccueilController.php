<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

// importer les entité 
use App\Entity\Produit;

final class AccueilController extends AbstractController
{

    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        
        
        return $this->render('accueil/index.html.twig');
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
