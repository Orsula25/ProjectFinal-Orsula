<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Form\VenteType;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\TypeMouvement;
use App\Repository\MouvementStockRepository;

#[Route('/vente')]
final class VenteController extends AbstractController
{
    #[Route(name: 'app_vente_index', methods: ['GET'])]
    public function index(VenteRepository $venteRepository): Response
    {
        return $this->render('vente/index.html.twig', [
            'ventes' => $venteRepository->findAll(),
        ]);
        $mouvementRepo -> enregistrerMouvement(
            $produit,
            $quantite,
            TypeMouvement::SORTIE
        );
    }

    #[Route('/new', name: 'app_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vente = new Vente();
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);


        // on traite le détail de chaque produit dans la vente
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($vente->getDetailVentes() as $detail) {
                // on fixe la Vente
                $detail->setVente($vente);
                // updater la quantité du produit
                $qteActuelle = $detail->getProduit()->getQuantiteStock();
                $qteFinale = $qteActuelle - $detail->getQuantite();
                $detail->getProduit()->setQuantiteStock($qteFinale);

                // si le stock est sous le seuil, créer une commande
                // - Créer objet Commande
                // - Fixer le fournisseur
                // - Fixer la date
                // - Fixer le statut
                // - Fixer le total
                // - Fixer la vente
                // - Enregistrer

                // opt: créer un MouvementStock et l'enregistrer

                


                $detail->calculerSousTotal();
            }
            // calcul total 

            $total = 0;
            foreach ($vente->getDetailVentes() as $detail) {
                $total += (float)$detail->getSousTotal();
            }
            $vente ->setMontantTotal($total);

            // generer un evenement pour creer commande si le stock  

            $entityManager->persist($vente);
            $entityManager->flush();

            $this->addFlash('success', 'Vente enregistrée avec succès✅');
            return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vente/new.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vente_show', methods: ['GET'])]
    public function show(Vente $vente): Response
    {
        return $this->render('vente/show.html.twig', [
            'vente' => $vente,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vente modifiée avec succès✅');

            return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vente/edit.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vente_delete', methods: ['POST'])]
    public function delete(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vente->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vente);
            $entityManager->flush();
            }

        $this->addFlash('success', 'Vente supprimée avec succès✅');
        return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
    }


    public function findMouvementsByProduit(Produit $produit): array
    {
        return $this->createQueryBuilder('m')
        ->where('m.produit = :prod')
        ->setParameter('prod', $produit)
        ->orderBy('m.dateMouvement', 'DESC')
        ->getQuery()
        ->getResult();
    }
}

