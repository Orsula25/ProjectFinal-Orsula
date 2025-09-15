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

        $adresse = [

            'rue' => ' rue de la paix',
            'numero' => '1',
            'codePostal' => '1800',
            'ville' => 'Bruxelles'
        ];

        

       $vars = [
        'nom' => 'Jean Dupont',// passage de variable simple 
        'hobbies' => "courses",
        'dateNaissance' => new \DateTime("2020-1-6"),// passage d'un objet DateTime

        'adresse' => $adresse,
      ];


        return $this->render('accueil/index.html.twig', $vars);
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
