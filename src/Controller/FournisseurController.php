<?php

namespace App\Controller;

use App\Repository\FournisseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\FournisseurType;
use App\Entity\Fournisseur;


final class FournisseurController extends AbstractController
{
    #[Route('/fournisseur', name: 'app_fournisseur_index')]
    public function index(FournisseurRepository $FournisseurRepository): Response
    {
        return $this->render('fournisseur/index.html.twig', [
            'fournisseurs' => $FournisseurRepository->findAll(),
        ]);
    }


    #[Route('/Fournisseur/new', name: 'app_fournisseur_new', methods:['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager):
    Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class,$fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($fournisseur);
            $entityManager->Flush();
            $this->addFlash('success', 'Fournisseur enregistré avec succès✅');
            return $this->redirectToRoute('app_fournisseur_index',[],Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/new.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);

    }
    #[Route('/fournisseur/{id}/edit', name:'app_fournisseur_edit', methods:['GET','POST'])]
    public function edit(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response

    {

        $form = $this->createForm(FournisseurType::class,$fournisseur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->Flush();
            $this->addFlash('success', 'Fournisseur modifié avec succès✅');
            return $this->redirectToRoute('app_fournisseur_index',[],Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/edit.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);


    }

    #[Route('/fournisseur/{id}', name:'app_fournisseur_show', methods:['GET'])]
    public function show(Fournisseur $fournisseur): Response
    {
        return $this->render('fournisseur/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }


    #[Route('/fournisseur/{id}', name:'app_fournisseur_delete', methods:['POST'])]
    public function delete(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        if($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->request->get('_token'))){
            $entityManager->remove($fournisseur);
            $entityManager->flush();
            $this->addFlash('success', 'Fournisseur supprimé avec succès✅');
            return $this->redirectToRoute('app_fournisseur_index',[],Response::HTTP_SEE_OTHER);
        }else{
            $this->addFlash('error', 'Token CSRF Invalide❌ (suppression annulée)');
        }


    return $this->redirectToRoute('app_fournisseur_index',[],Response::HTTP_SEE_OTHER);
    }


}
