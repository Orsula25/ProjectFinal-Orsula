<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/entreprise')]
final class EntrepriseController extends AbstractController
{
    #[Route('/', name: 'app_entreprise_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();
        $entreprise = $user?->getEntreprise();

        if (!$entreprise) {
            $this->addFlash('warning', 'Aucune entreprise n\'est associée à votre compte.');
            return $this->redirectToRoute('app_accueil');
        }

        return $this->redirectToRoute('app_entreprise_edit', [
            'id' => $entreprise->getId(),
        ]);
    }

#[Route('/new', name: 'app_entreprise_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $entreprise = new Entreprise();
    $form = $this->createForm(EntrepriseType::class, $entreprise);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        //  je récupère l'utilisateur connecté (l'admin)
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        //  je lie l’entreprise à cet utilisateur (OneToOne)
        
        $user->setEntreprise($entreprise);
        // si tu as l'autre côté dans Entreprise :
        // $entreprise->setUtilisateur($user);

        // je enregistre le tout
        $entityManager->persist($entreprise);
        // $entityManager->persist($user); // seulement si pas de cascade
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise_index');
    }

    return $this->render('entreprise/new.html.twig', [
        'entreprise' => $entreprise,
        'form' => $form,
    ]);
}


       #[Route('/{id}', name: 'app_entreprise_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entreprise_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise_index');
        }

        return $this->render('entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entreprise_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($entreprise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_entreprise_index');
    }


    #[Route('/mon-entreprise', name: 'app_entreprise_profil', methods: ['GET'])]
    public function profil(): Response
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();
        $entreprise = $user?->getEntreprise();

        if (!$entreprise) {
            $this->addFlash('warning', 'Aucune entreprise n\'est associée à votre compte.');
            return $this->redirectToRoute('app_entreprise_index');
        }

        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }
}
