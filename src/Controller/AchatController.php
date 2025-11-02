<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Form\AchatType;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\DetailAchat;
use App\Enum\TypeMouvement;

#[Route('/achat')]
final class AchatController extends AbstractController
{
    #[Route(name: 'app_achat_index', methods: ['GET'])]
    public function index(Request $request,AchatRepository $achatRepository): Response
    {
              // page 1 (par defait) 
        $page = max(1, $request->query->get('page', 1));

        // nombre elemen par page 

        $limit = 5;

        // à partir de quel enregistrement on commence 
        $offset = ($page - 1) * $limit;

        // combien de produit au total 
        $total = $achatRepository->count([]);

        // on récupère uniquement les 5 prosuits de la page + tri par le dernier ajouter d'abord 
        $achats = $achatRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')      // derniers ajoutés en premier
            ->setFirstResult($offset)      // on saute les précédents
            ->setMaxResults($limit)        // on en prend 5
            ->getQuery()
            ->getResult()
        ;

        return $this->render('achat/index.html.twig', [
            'achats' => $achats,
            'page'     => $page,
            'pages'    => (int) ceil($total / $limit),
        ]);;

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
       private function recalculeTotaux(Achat $achat): void
   {
       $totalTtc = 0.0;
   
       foreach ($achat->getDetailAchats() as $detail) {
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
       $achat->setMontantTotal(number_format($totalTtc, 2, '.', ''));
   }

    #[Route('/new', name: 'app_achat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $achat = new Achat();

        // ligne d'ajout par defaut 
        $achat->addDetailAchat(new DetailAchat());
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);


        // on enregistre l'Achat
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            //calcule des sous totaux pour chaque détail 
            foreach ($achat->getDetailAchats() as $detail) {

                // updater la quantité du produit
                $qteActuelle = $detail->getProduit()->getQuantiteStock();
                $qteFinale = $qteActuelle + $detail->getQuantite();
                $detail->getProduit()->setQuantiteStock($qteFinale);
                
                
                
                // on fixe l'Achat
                $detail->setAchat($achat);
                $detail->calculerSousTotal();
            }

            // recalcul du total de l'achat 
            $this->recalculeTotaux($achat);
            $entityManager->persist($achat);
            $entityManager->flush();

            $this->addFlash('success', 'Achat enregistré avec succès✅');
            return $this->redirectToRoute('app_achat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('achat/new.html.twig', [
            'achat' => $achat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_achat_show', methods: ['GET'])]
    public function show(Achat $achat): Response
    {
        return $this->render('achat/show.html.twig', [
            'achat' => $achat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_achat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //calcule des sous totaux pour chaque détail 
            foreach ($achat->getDetailAchats() as $detail) {
               
                $detail->setAchat($achat);
                $detail->calculerSousTotal();
            }

            $this->recalculeTotaux($achat);
            $entityManager->flush();
            $this->addFlash('success', 'Achat modifié avec succès✅');
            return $this->redirectToRoute('app_achat_index', [], Response::HTTP_SEE_OTHER);


        

        }

        return $this->render('achat/edit.html.twig', [
            'achat' => $achat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_achat_delete', methods: ['POST'])]
    public function delete(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $achat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($achat);
            $entityManager->flush();

            $this->addFlash('success', 'Achat supprimé avec succès✅');
        }

        return $this->redirectToRoute('app_achat_index', [], Response::HTTP_SEE_OTHER);
    }
}
