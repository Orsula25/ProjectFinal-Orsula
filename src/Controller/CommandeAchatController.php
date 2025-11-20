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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;


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

      // VOIR LA COMMANDE
    #[Route('/{id}', name: 'app_commande_achat_show', methods: ['GET'])]
    public function show(CommandeAchat $commande): Response
    {
        return $this->render('commande_achat/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    // ENVOYER LA COMMANDE
#[Route('/{id}/envoyer', name: 'app_commande_achat_envoyer', methods: ['POST'])]
public function envoyer(
    CommandeAchat $commande,
    Request $request,
    EntityManagerInterface $em,
    MailerInterface $mailer
): Response {
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
            $produit->incEnCommande($qte);
            $em->persist($produit);
        }
    }

    $commande->setStatut(CommandeAchat::STATUT_ENVOYEE);
    $em->flush();

    // ENVOI DU MAIL 

    // récupère le fournisseur (en sécurité)
    $fournisseur = $commande->getFournisseur();
    $toEmail     = $fournisseur?->getEmail() ?? 'test@example.com';

    // construit le sujet proprement
    $sujet = sprintf(
        'Nouvelle commande %s',
        $commande->getReference() ?? ('#'.$commande->getId())
    );

    $email = (new Email())
        ->from('no-reply@logix-gstock.test')
        ->to($toEmail)
        ->subject($sujet)   // subject prend UN seul argument
        ->html(
            $this->renderView('emails/commande_achat.html.twig', [
                'commande'    => $commande,
                'fournisseur' => $fournisseur,
            ])
        );

    $mailer->send($email);

    $this->addFlash('success', 'Commande envoyée ✅ (email transmis au fournisseur)');
    return $this->redirectToRoute('app_commande_achat_index');
}


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

    // Mise à jour des stocks
  foreach ($commande->getLignesCommande() as $ligne) {
    $p   = $ligne->getProduit();
    $qte = $ligne->getQuantite();

    if ($p && $qte > 0) {
        

        //on utilise le helper
        $p->decEnCommande($qte);

        // on ajoute au stock réel
        $p->setQuantiteStock(
            $p->getQuantiteStock() + $qte
        );

        $em->persist($p);
    }
}

    // Créer l'achat
    $achat = new Achat();
    $achat->setDateAchat(new \DateTimeImmutable());
    $achat->setFournisseur($commande->getFournisseur());
    $achat->setEtat('Réceptionné');
    $achat->setReference($commande->getReference());

    // Créer les détails d'achat
    foreach ($commande->getLignesCommande() as $ligneCmd) {
        $produit = $ligneCmd->getProduit();

        if (!$produit) {
            continue;
        }

        $detail = new DetailAchat();
        $detail->setAchat($achat);
        $detail->setProduit($produit);
        $detail->setQuantite($ligneCmd->getQuantite());

    
        $detail->setPrixUnitaire($produit->getPrixAchat());

        // calcule le sous-total (quantité × prix unitaire)
        $detail->calculerSousTotal();

        $achat->addDetailAchat($detail);
        $em->persist($detail);
    }

    // recalculer explicitement le montant total de l'achat
    $achat->recalculerMontantTotal();

    // Mettre à jour le statut de la commande
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

    // pdf commande 
    #[Route('/{id}/pdf', name: 'app_commande_achat_pdf', methods: ['GET'])]
    public function pdf(CommandeAchat $commande): Response
    {
          // Options Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true); // des images / fonts externes

        $dompdf = new Dompdf($options);

        // HTML à partir du Twig
        $html = $this->renderView('commande_achat/pdf.html.twig', [
            'commande' => $commande,
        ]);

        // Générer le PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        // Réponse HTTP avec headers PDF
        return new Response(
            $output,
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="commande-'.$commande->getId().'.pdf"',
            ]
        );
    }
}


