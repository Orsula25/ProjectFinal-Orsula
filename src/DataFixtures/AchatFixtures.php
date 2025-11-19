<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use DateTimeImmutable;
use App\Entity\Achat;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use App\Entity\DetailAchat;

final class AchatFixtures extends Fixture implements DependentFixtureInterface
{
    private const NB_ACHATS = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= self::NB_ACHATS; $i++) {
            $achat = new Achat();
            $achat->setDateAchat(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $achat->setEtat($faker->randomElement(['en_cours', 'termine']));
            $achat->setDateCreation(new DateTimeImmutable());
            $achat->setDateModification(new DateTimeImmutable());

            /** @var Fournisseur $fournisseur */
            $fournisseur = $this->getReference('fournisseur' . rand(1, 10), Fournisseur::class);
            $achat->setFournisseur($fournisseur);

            $totalTtc = 0.0;

            // 1 à 3 lignes d'achat par facture
            $nbLignes = $faker->numberBetween(1, 3);
            for ($l = 1; $l <= $nbLignes; $l++) {
                /** @var Produit $produit */
                $produit = $this->getReference('produit' . rand(1, 10), Produit::class);

                $qte = $faker->numberBetween(1, 20);

                // Prix de vente du produit
                $prixVente = (float) $produit->getPrixUnitaire();

                // Prix d'achat = -25 % (marge 25 %)
                $prixAchat = round($prixVente * 0.75, 2);

                $detail = new DetailAchat();
                $detail->setProduit($produit);
                $detail->setAchat($achat);
                $detail->setQuantite($qte);
                $detail->setPrixUnitaire($prixAchat);

                // calcul HT + TVA
                $ligneHt = $qte * $prixAchat;
                $tauxTva = $this->normaliseTaux($produit->getTva());
                $ligneTtc = $ligneHt * (1 + $tauxTva);

                // Sous-total HT (comme dans ton code)
                $detail->setSousTotal(number_format($ligneHt, 2, '.', ''));

                $totalTtc += $ligneTtc;

                // On met à jour le stock du produit
                $produit->setQuantiteStock($produit->getQuantiteStock() + $qte);

                $manager->persist($detail);
                $achat->addDetailAchat($detail);
            }

            // Montant total TTC de l'achat
            $achat->setMontantTotal(number_format($totalTtc, 2, '.', ''));

            $manager->persist($achat);
            $this->addReference('achat' . $i, $achat);
        }

        $manager->flush();
    }

    private function normaliseTaux(?string $tva): float
    {
        if ($tva === null || $tva === '') {
            return 0.0;
        }
        $val = (float) $tva;
        return $val > 1 ? $val / 100 : $val;
    }

    public function getDependencies(): array
    {
        // On a besoin des fournisseurs ET des produits pour créer les achats
        return [
            FournisseurFixtures::class,
            ProduitFixtures::class,
        ];
    }
}
