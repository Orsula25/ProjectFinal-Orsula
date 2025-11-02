<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\CommandeAchat;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeAchatRepository;
use App\Form\CommandeAchatType;


// voir la commande 

#[Route('/commande')]
    class CommandeAchatController extends AbstractController
    {

        //liste 

       
    #[Route('/', name: 'app_commande_achat_index', methods: ['GET'])]
    public function index(Request $request,CommandeAchatRepository $repo): Response
    {
             // page 1 (par defait) 
        $page = max(1, $request->query->get('page', 1));

        // nombre elemen par page 

        $limit = 5;

        // à partir de quel enregistrement on commence 
        $offset = ($page - 1) * $limit;

        // combien de produit au total 
        $total = $repo->count([]);

        // on récupère uniquement les 5 prosuits de la page + tri par le dernier ajouter d'abord 
        $commandes = $repo->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')      // derniers ajoutés en premier
            ->setFirstResult($offset)      // on saute les précédents
            ->setMaxResults($limit)        // on en prend 5
            ->getQuery()
            ->getResult()
        ;

        return $this->render('commande_achat/index.html.twig', [
            'commandes' => $commandes,
            'page'     => $page,
            'pages'    => (int) ceil($total / $limit),
        ]);;
    }

    // nouvelle commande 
    #[Route('/nouvelle', name: 'app_commande_achat_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $commande = new CommandeAchat();
        $commande->setStatut(CommandeAchat::STATUT_BROUILLON);
        $commande->setDate(new \DateTime());

        $form = $this->createForm(CommandeAchatType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // gerer une référence du type PO-...
            if (!$commande->getReference()) {
                $commande->setReference('PO-'.date('Ymd').'-'.random_int(100,999));
            }

            $em->persist($commande);
            $em->flush();

            $this->addFlash('success', 'Commande créée en brouillon.');
            return $this->redirectToRoute('app_commande_achat_show', [
                'id' => $commande->getId(),
            ]);
        }

        return $this->render('commande_achat/new.html.twig', [
            'form' => $form,
        ]);
    }




            // envoyer la commande 
        #[Route('/{id}/envoyer', name: 'app_commande_achat_envoyer', methods: ['POST'])]
    public function envoyer(CommandeAchat $commande, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('envoyer'.$commande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        if ($commande->isReceptionnee() || $commande->isAnnulee()) {
            $this->addFlash('warning', 'Cette commande ne peut plus être envoyée.');
            return $this->redirectToRoute('app_commande_achat_index');
        }

        if ($commande->isEnvoyee()) {
            $this->addFlash('info', 'Commande déjà envoyée.');
            return $this->redirectToRoute('app_commande_achat_index');
        }

        // ➕ ajouter les quantités dans "en commande" pour chaque produit
        foreach ($commande->getLignesCommande() as $ligne) {
            $produit = $ligne->getProduit();
            $qte     = $ligne->getQuantite();

            if ($produit && $qte > 0) {
                $produit->setEnCommande($produit->getEnCommande() + $qte);
                $em->persist($produit);
            }
        }

        $commande->setStatut(CommandeAchat::STATUT_ENVOYEE);
        $em->flush();

        $this->addFlash('success', 'Commande envoyée ✅');
        return $this->redirectToRoute('app_commande_achat_index');
    }


            // voir la commande 
    #[Route('/{id}', name: 'app_commande_achat_show', methods: ['GET'])]
        public function show(CommandeAchat $commande): Response
        {
            return $this->render('commande_achat/show.html.twig', [
                'commande' => $commande,
            ]);
        }
            // recevoir la commande 

            #[Route('/{id}/recevoir', name: 'app_commande_achat_reception', methods: ['POST'])]
            public function recevoir(CommandeAchat $commande,Request $request, EntityManagerInterface $em): Response
            {


                // CSRF
                if (!$this->isCsrfTokenValid('reception' .$commande->getId(), $request->getPayload()->getString('_token'))) {
                    throw $this->createAccessDeniedException('Token invalide');
                }

                // éviter les double réception 

                if ($commande->isReceptionnee()) {
                    $this->addFlash('warning', 'Cette commande a deja été réceptionnée');
                    return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
                }

                // forcer que la commande ait bien été encoyé avant de la recevoir
                if (!$commande->isEnvoyee()) {
                    $this->addFlash('warning', 'Tu dois d\'abord envoyer la commande avant de la réceptionner.');
                    return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
                }

                // mettre à jour le stock des produits pour chaque ligne de commande
                foreach ($commande->getLignesCommande() as $ligne) {
                    $p = $ligne->getProduit();
                    $qte = $ligne->getQuantite();
                
                    if ($p && $qte > 0) {
                        // on lenleve du "en commande"
                        $p->setEnCommande(
                            max(0, $p->getEnCommande() - $qte)
                        );
                    
                // on ajoute au stock réel
                $p->setQuantiteStock($p->getQuantiteStock() + $qte);

                $em->persist($p);
            }
        }


            // mettre à jour le statut de la commande (mettre RECEPTIONNEE)

            $commande->setStatut(CommandeAchat::STATUT_RECEPTIONNEE);
            $em->flush();

            $this->addFlash('success', 'Commande réceptionnée et stock mis à jour ✅');
            return $this->redirectToRoute('app_commande_achat_index');
        
    }



  


    // annuler la commande 

    #[Route('/{id}/annuler', name: 'app_commande_achat_annuler', methods: ['POST'])]
    public function annuler(CommandeAchat $commande, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('annuler'.$commande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        if ($commande->isReceptionnee()) {
            $this->addFlash('warning', 'Impossible d\'annuler : la commande est déjà réceptionnée.');
            return $this->redirectToRoute('app_commande_achat_index');
        }

        // si elle était envoyée, il faut restituer le "en commande"
        if ($commande->isEnvoyee()) {
            foreach ($commande->getLignesCommande() as $ligne) {
                $produit = $ligne->getProduit();
                $qte     = $ligne->getQuantite();

                if ($produit && $qte > 0) {
                    $produit->setEnCommande(
                        max(0, $produit->getEnCommande() - $qte)
                    );
                    $em->persist($produit);
                }
            }
        }

        $commande->setStatut(CommandeAchat::STATUT_ANNULEE);
        $em->flush();

        $this->addFlash('success', 'Commande annulée ❌');
        return $this->redirectToRoute('app_commande_achat_index');
    }


    

}

