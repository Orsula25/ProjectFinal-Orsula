<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\ReapprovisionnementType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ReapprovisionnementService;
use App\Entity\CommandeAchat;
use App\Entity\LigneCommande;





#[Route('/produit')]
final class ProduitController extends AbstractController
{
#[Route(name: 'app_produit_index', methods: ['GET'])]
public function index(Request $request, ProduitRepository $produitRepository): Response
{
    $q     = trim($request->query->get('q', ''));
    $sort  = $request->query->get('sort', 'recent');   // recent | name_asc | name_desc
    $page  = max(1, $request->query->getInt('page', 1));
    $limit = 5;
    $offset = ($page - 1) * $limit;

    $searchTerm = $q !== '' ? $q : null;

    $total    = $produitRepository->countSearch($searchTerm);
    $produits = $produitRepository->findBySearch($searchTerm, $sort, $limit, $offset);

    // termes pour l’autocomplétion (nom + référence)
    $termes = $produitRepository->findAllSearchTerms();

    if ($request->isXmlHttpRequest()) {
        return $this->render('produit/_liste.html.twig', [
            'produits' => $produits,
            'page'     => $page,
            'pages'    => (int) ceil($total / $limit),
            'q'        => $q,
            'sort'     => $sort,
        ]);
    }

    return $this->render('produit/index.html.twig', [
        'produits' => $produits,
        'page'     => $page,
        'pages'    => (int) ceil($total / $limit),
        'q'        => $q,
        'sort'     => $sort,
        'termes'   => $termes, //  pour le datalist (Autocplétion)
    ]);
}



    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    // route pour le reapprovisionnement

    #[Route('/{id}/reappro', name: 'produit_reappro', methods: ['GET', 'POST'])]
    public function reappro(Produit $produit, Request $request, ReapprovisionnementService $proposer, EntityManagerInterface $entityManager): Response

    {

        // pour eviter que l'accès direct quand le prouit nest pas en seuil 

        $sousSeuil = $produit->getStockMin()!== null && $produit->getQuantiteStock() <= $produit->getStockMin();
        $rupture = $produit->getQuantiteStock() <= 0;

        if (!$sousSeuil && !$rupture) {
            $this->addFlash('warning', 'Le produit n\'est ni seuil ni en rupture');
            return $this->redirectToRoute('app_produit_index');
        }

        // pour initialiser le formulaire avec les données du produit

       $suggestion = $proposer->quantiteProposee($produit);

    // Récupérer les fournisseurs liés au produit via la table pivot
    $fournisseurChoices = [];
    foreach ($produit->getProduitFournisseurs() as $pf) {
        if ($pf->getFournisseur()) {
            $fournisseurChoices[] = $pf->getFournisseur();
        }
    }

    // Pré-sélection par défaut 
    $defaultFournisseur = $fournisseurChoices[0] ?? null;

    $form = $this->createForm(ReapprovisionnementType::class, null, [
        'data' => [
            'quantite'    => $suggestion,
            'fournisseur' => $defaultFournisseur, // peut être null, pas grave
        ],
        // option custom qu'on lit dans le FormType
        'fournisseur_choices' => $fournisseurChoices,
    ]);

        // pour traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $fournisseur = $form->get('fournisseur')->getData();
          $quantite = (int)$form->get('quantite')->getData();

          //1 creer la commande 
          $commande = new CommandeAchat();
          $commande->setReference($this->genRef());
          $commande->setFournisseur($fournisseur);
          $commande->setDate(new \DateTime());
          $commande->setStatut('DRAFT');

          //2.Ligne unique pour CE produit 

          $ligne = new LigneCommande();
          $ligne->setProduit($produit);
          $ligne->setQuantite($quantite);
         
          // attacher avec add (important pour la cascade)
          $commande->addLignesCommande($ligne);

          
// persister les objets
          $entityManager->persist($commande);
          $entityManager->flush();

// rediriger vers la page commande

        return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
    }



      return $this->render('produit/reappro.html.twig', [
        'produit' => $produit,
        'form' => $form ->createView(),
        'suggestion' => $suggestion,
    ]);
    }

    // helper pour la reference 
    private function genRef(): string
    {
        return 'PO-' . (new \DateTime())->format('Ymd') . '-' . random_int(100, 999);
    }
}

  

