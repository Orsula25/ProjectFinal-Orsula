<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\Request;

// Controller pour les formulaires de clients

final class FormsClientController extends AbstractController{

    #[Route('/client/form', name: 'app_form_show')]
    public function afficherForm(): Response
    {

        $formClient = $this->createForm(ClientType::class);
        $vars = [
            'formClient' => $formClient->createView(),
        ];
        
        // faire le rendu de la vue .Envoyer l'objet formProduit
        return $this->render('forms_client/afficher_form.html.twig', $vars);
    }

    // Controller pour inserer un client
#[Route('/client/new', name: 'app_form_insert')]
public function insererClient(Request $request, EntityManagerInterface $em):Response{
    $client = new Client();
    
    $formClient = $this->createForm(ClientType::class, $client);

    $formClient->handleRequest($request);

    // le formulaire est soumis et valide
    if ($formClient->isSubmitted()){
        $em->persist($client);
        $em->flush();
        return $this->redirectToRoute('app_form_show');
    }

    // le formulaire n'est pas soumis ou invalide
    else{
        $vars = ['formClient' => $formClient->createView()];
        return $this->render('forms_client/afficher_form_insert.html.twig', $vars);
    }
}

// Controller pour afficher tous les clients

#[Route('/client/all', name: 'app_form_all')]
public function afficherClients(EntityManagerInterface $em): Response{
    $rep = $em->getRepository(Client::class);
    $arrayClients = $rep->findAll();
    $vars = [
        'clients' => $arrayClients
    ];
    return $this->render('forms_client/afficher_clients.html.twig', $vars);
}








}


