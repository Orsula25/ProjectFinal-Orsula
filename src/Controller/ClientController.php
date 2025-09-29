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
    #[Route('/client', name: 'app_client')]
    public function index(ClientRepository $ClientRepository): Response
    {

        return $this->render('client/index.html.twig', [
            'clients' => $ClientRepository->findAll(),
        ]);
    }



    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(Clienttype::class, $client);
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





    #[Route('/client/{id}', name: 'app_client_show')]
    public function show(Client $client): Response
    {
        
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
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
