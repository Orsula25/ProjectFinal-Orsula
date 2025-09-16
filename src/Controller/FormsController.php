<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
useSymfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class FormsController extends AbstractController
{
    #[Route('/afficher/forms', name: 'app_forms')]

    public function afficherForm(): Response
    {
        $formProduit = $this->createForm(ProduitType::class);

        $vars = [
            'formProduit' => $formProduit->createView(),
        ];
        // faire le rendu de la vue .Envoyer l'objet formProduit
        return $this->render('forms/afficher_form.html.twig', $vars);
    }



    #[Route('/forms/inserer/produit')]
    public function insererProduit(Request $request, EntityManagerInterface $em):Response{
        $produit = new Produit();
        
        $formProduit = $this->createForm(ProduitType::class, $produit);

        
        $formProduit = $this->createform(ProduitType::class, $produit);
        $formProduit->handleRequest($request);

        // ici on peut avoir 2 situation différentes 

        //1- le formulaire est soumis et valide
        if ($formProduit->isSubmitted()) {
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_forms');
        
        }

        //2- on ne vient pas dun submit, alors on affiche le formulaire
      else {
        $vars = ['formProduit' => $formProduit->];
        return $this->render('forms/afficher_form_insert.html.twig', $vars);
      }
            
        
    
   
}

