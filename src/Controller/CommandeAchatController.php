<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\CommandeAchat;
use Doctrine\ORM\EntityManagerInterface;


// voir la commande 

#[Route('/commande')]
    class CommandeAchatController extends AbstractController
    {

            #[Route('/{id}', name: 'app_commande_achat_show', methods: ['GET'])]
    public function show(CommandeAchat $commande): Response
    {
        return $this->render('commande_achat/show.html.twig', [
            'commande' => $commande,
        ]);
    }
        // recevoir la commande 

        #[Route('/{id}/recevoir', name: 'app_commande_achat_reception', methods: ['POST'])]
        public function recevoir(CommandeAchat $commande,Request $request, EntityManagerInterface $entityManager): Response
        {
            // CSRF
            if (!$this->isCsrfTokenValid('reception' .$commande->getId(), $request->getPayload()->getString('_token'))) {
                throw $this->createAccessDeniedException('Token invalide');
            }

            // éviter les double réception 

            if ($commande->getStatut() !== 'DRAFT') {
                $this->addFlash('warning', 'Cette commande n\'est plus en brouillon');
                return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
            }

            // incrémenter le stock des produits
             foreach ($commande->getLignesCommande() as $ligne) {
            $p = $ligne->getProduit();
            if ($p) {
                $q = (int) $ligne->getQuantite();
                $p->decEnCommande($q); // enCommande
                $p->setQuantiteStock($p->getQuantiteStock() + $q); // stock réel
            }
        }

        $commande->setStatut('RECEIVED');
        $entityManager->flush();

        $this->addFlash('success', 'Commande réceptionnée et stock mis à jour ✅');
        return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
    }
}

