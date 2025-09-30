<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ClientType;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client_index')]
    public function index(ClientRepository $ClientRepository): Response
    {

        return $this->render('client/index.html.twig', [
            'clients' => $ClientRepository->findAll(),
        ]);
    }



    #[Route('/client/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client enregistré avec succès✅');
            return $this->redirectToRoute('app_client_index',[],Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/client/{id}', name:'app_client_show')]





    #[Route('/client/{id}', name: 'app_client_show',methods: ['GET'])]
    public function show(Client $client): Response
    {
        
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }


    #[Route('/client/{id}/edit', name:'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Client modifié avec succès✅');
        return $this->redirectToRoute('app_client_index',[],Response::HTTP_SEE_OTHER);
        
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/client/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response 
    {
        if($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))){
            $entityManager->remove($client);
            $entityManager->flush();
            $this->addFlash('success', 'Client supprimé avec succès✅');
        }
        else{
            $this->addFlash('error', 'Token CSRF Invalide❌ (suppression annulée)');
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
       

    
    public function recherche(EntityManagerInterface $entityManager): Response

    {
       $clients = $em ->getRepository(Client::class)->findClientsByNom($nom); 
       $query = $em -> createQuery("SELECT c FROM App\Entity\Client c WHERE c.nom LIKE :nom");
       $query->setParameter('nom', "%$nom%");
       $clients = $query->getResult();
       return $this->render('client/index.html.twig', [
           'clients' => $clients,
       ]);
       
    }
}
