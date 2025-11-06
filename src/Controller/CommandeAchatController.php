<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\CommandeAchat;
use App\Entity\DetailAchat;
use App\Form\CommandeAchatType;
use App\Repository\CommandeAchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande')]
class CommandeAchatController extends AbstractController
{
    // LISTE
    #[Route('/', name: 'app_commande_achat_index', methods: ['GET'])]
    public function index(Request $request, CommandeAchatRepository $repo): Response
    {
        $page   = max(1, $request->query->getInt('page', 1));
        $limit  = 5;
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);

        $commandes = $repo->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        return $this->render('commande_achat/index.html.twig', [
            'commandes' => $commandes,
            'page'      => $page,
            'pages'     => (int) ceil($total / $limit),
        ]);
    }

    // NOUVELLE COMMANDE
    #[Route('/nouvelle', name: 'app_commande_achat_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $commande = new CommandeAchat();
        $commande->setStatut(CommandeAchat::STATUT_BROUILLON);
        $commande->setDate(new \DateTime());

        $form = $this->createForm(CommandeAchatType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

    // ENVOYER LA COMMANDE
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

    // VOIR LA COMMANDE
    #[Route('/{id}', name: 'app_commande_achat_show', methods: ['GET'])]
    public function show(CommandeAchat $commande): Response
    {
        return $this->render('commande_achat/show.html.twig', [
            'commande' => $commande,
        ]);
    }

        // RÉCEPTIONNER LA COMMANDE + CRÉER L'ACHAT
    // RÉCEPTIONNER LA COMMANDE + CRÉER L'ACHAT
#[Route('/{id}/recevoir', name: 'app_commande_achat_reception', methods: ['POST'])]
#[Route('/commande-achat/{id}/reception', name: 'app_commande_achat_reception_legacy', methods: ['POST'])]
public function recevoir(CommandeAchat $commande, Request $request, EntityManagerInterface $em): Response
{
    if (!$this->isCsrfTokenValid('reception'.$commande->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token invalide');
    }

    if ($commande->isReceptionnee()) {
        $this->addFlash('warning', 'Cette commande a déjà été réceptionnée');
        return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
    }

    if (!$commande->isEnvoyee()) {
        $this->addFlash('warning', 'Tu dois d\'abord envoyer la commande avant de la réceptionner.');
        return $this->redirectToRoute('app_commande_achat_show', ['id' => $commande->getId()]);
    }

    // 1) Mise à jour des stocks
    foreach ($commande->getLignesCommande() as $ligne) {
        $p   = $ligne->getProduit();
        $qte = $ligne->getQuantite();

        if ($p && $qte > 0) {
            // on enlève du "en commande"
            $p->setEnCommande(
                max(0, $p->getEnCommande() - $qte)
            );

            // on ajoute au stock réel
            $p->setQuantiteStock(
                $p->getQuantiteStock() + $qte
            );

            $em->persist($p);
        }
    }

    // 2) Créer l'achat
    $achat = new Achat();
    $achat->setDateAchat(new \DateTimeImmutable());
    $achat->setFournisseur($commande->getFournisseur());
    $achat->setEtat('Réceptionné');
    $achat->setReference($commande->getReference());

    // 3) Créer les détails d'achat
    foreach ($commande->getLignesCommande() as $ligneCmd) {
        $produit = $ligneCmd->getProduit();

        if (!$produit) {
            continue;
        }

        $detail = new DetailAchat();
        $detail->setAchat($achat);
        $detail->setProduit($produit);
        $detail->setQuantite($ligneCmd->getQuantite());

        // ⚠️ ADAPTE CE GETTER AU TIEN SUR Produit :
        //   - getPrixAchat()
        //   - ou getPrix()
        //   - ou getPrixHt()
        $detail->setPrixUnitaire($produit->getPrixAchat());

        // calcule le sous-total (quantité × prix unitaire)
        $detail->calculerSousTotal();

        $achat->addDetailAchat($detail);
        $em->persist($detail);
    }

    // 4) recalculer explicitement le montant total de l'achat
    $achat->recalculerMontantTotal();

    // 5) Mettre à jour le statut de la commande
    $commande->setStatut(CommandeAchat::STATUT_RECEPTIONNEE);

    $em->persist($achat);
    $em->flush();

    $this->addFlash('success', 'Commande réceptionnée, stock mis à jour et achat créé ✅');
    return $this->redirectToRoute('app_commande_achat_index');
}


    // ANNULER LA COMMANDE
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
