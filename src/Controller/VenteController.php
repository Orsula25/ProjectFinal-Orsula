<?php

namespace App\Controller;

use App\Entity\DetailVente;
use App\Entity\Vente;
use App\Form\VenteType;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use App\Enum\TypeMouvement;
// use App\Repository\MouvementStockRepository;

#[Route('/vente')]
final class VenteController extends AbstractController
{
    #[Route(name: 'app_vente_index', methods: ['GET'])]
    public function index(Request $request,VenteRepository $venteRepository): Response
    {
            // page 1 (par defait) 
        $page = max(1, $request->query->get('page', 1));

        // nombre elemen par page 

        $limit = 5;

        // à partir de quel enregistrement on commence 
        $offset = ($page - 1) * $limit;

        // combien de produit au total 
        $total = $venteRepository->count([]);

        // on récupère uniquement les 5 prosuits de la page + tri par le dernier ajouter d'abord 
        $ventes = $venteRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')      // derniers ajoutés en premier
            ->setFirstResult($offset)      // on saute les précédents
            ->setMaxResults($limit)        // on en prend 5
            ->getQuery()
            ->getResult()
        ;

        return $this->render('vente/index.html.twig', [
            'ventes' => $ventes,
            'page'     => $page,
            'pages'    => (int) ceil($total / $limit),
        ]);
        
    }

    #[Route('/new', name: 'app_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vente = new Vente();

        // ligne d'ajout par defaut 
        $vente->addDetailVente(new DetailVente());
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);


        // on traite le détail de chaque produit dans la vente
        if ($form->isSubmitted() && $form->isValid()) {

            // stock + rattachement 
            foreach ($vente->getDetailVentes() as $detail) {
                $detail->setVente($vente);
                // updater la quantité du produit
                $qteActuelle = $detail->getProduit()->getQuantiteStock();
                $qteFinale = $qteActuelle - $detail->getQuantite();
                $detail->getProduit()->setQuantiteStock($qteFinale);
            }
                // si le stock est sous le seuil, créer une commande
                // - Créer objet Commande
                // - Fixer le fournisseur
                // - Fixer la date
                // - Fixer le statut
                // - Fixer le total
                // - Fixer la vente
                // - Enregistrer

                // opt: créer un MouvementStock et l'enregistrer

                
            // appele la foncton recalcule pour eviter une répitition de code 
            $this->recalculeTotaux($vente);

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

    // pour calculer la tva (le prix unitaire est un prix hors tva)
    private function normaliseTaux(?string $tva): float
    {
        if ($tva === null || $tva === '') {
            return 0.0;
        }
        $val = (float) $tva;
        return $val > 1 ? $val / 100 : $val;
    }

    // fonction recalcule des totaux aaprès la modification 
    private function recalculeTotaux(Vente $vente): void
{
    $totalTtc = 0.0;

    foreach ($vente->getDetailVentes() as $detail) {
        $produit = $detail->getProduit();

        // Prix unitaire de la ligne (HT) : priorité au PU saisi sur la ligne, sinon PU du produit
        $puLigne = $detail->getPrixUnitaire() !== null
            ? (float) $detail->getPrixUnitaire()
            : (float) $produit->getPrixUnitaire();

        // Si le PU de ligne était vide, on le renseigne pour cohérence en base
        if ($detail->getPrixUnitaire() === null) {
            $detail->setPrixUnitaire(number_format($puLigne, 2, '.', ''));
        }

        $qte  = (float) ($detail->getQuantite() ?? 0);
        $taux = $this->normaliseTaux($produit->getTva()); // ex: "21.00" -> 0.21

        // Montants de la ligne
        $ligneHt  = $qte * $puLigne;
        $ligneTva = $ligneHt * $taux;
        $ligneTtc = $ligneHt + $ligneTva;

        // Stocker le sous-total HT sur la ligne (champ existant)
        $detail->setSousTotal(number_format($ligneHt, 2, '.', ''));

        // Cumuler le TTC
        $totalTtc += $ligneTtc;
    }

    // Total TTC de la vente
    $vente->setMontantTotal(number_format($totalTtc, 2, '.', ''));
}


    #[Route('/{id}/edit', name: 'app_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

    

        if ($form->isSubmitted() && $form->isValid()) {

            // mise à jour du calcul du montant avant le flush 
            $this->recalculeTotaux($vente); 
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

 // a deplacer dans le repo permet de trouver les mouvements par produit
    // public function findMouvementsByProduit(Produit $produit): array
    // {
    //     return $this->createQueryBuilder('m')
    //     ->where('m.produit = :prod')
    //     ->setParameter('prod', $produit)
    //     ->orderBy('m.dateMouvement', 'DESC')
    //     ->getQuery()
    //     ->getResult();
    // }
}



