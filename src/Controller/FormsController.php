<?php

namespace App\Controller;


use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;



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



    #[Route('/forms/inserer/produit', name: 'app_form_inserer_produit')]
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
            return $this->redirectToRoute('app_form_afficher_produit');
        
        }

        //2- on ne vient pas dun submit, alors on affiche le formulaire
      else {
        $vars = ['formProduit' => $formProduit];
        return $this->render('forms/afficher_form_insert.html.twig', $vars);
      }

    }

    #[Route('/forms/afficher/produit', name: 'app_form_afficher_produit')]
    public function afficherProduit(EntityManagerInterface $em){
        //Envoyer à une vue tous les produit inserer dans la bd
        // la vue les affichera 
        $rep = $em->getRepository(Produit::class);
        $arrayProduits = $rep->findAll();
        $vars = [
            'produits' => $arrayProduits
        ];
        return $this->render('forms/afficher_animaux.html.twig', $vars);

            
    }
    
   
}

