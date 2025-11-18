<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Vente;
use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\DetailVente;

class VenteFixtures extends Fixture implements DependentFixtureInterface
{
    private const NB_VENTES = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // On récupère les références produits une seule fois
        $produits = [];
        for ($i = 1; $i <= 10; $i++) {
            /** @var Produit $p */
            $p = $this->getReference('produit' . $i, Produit::class);
            $produits[] = $p;
        }

        for ($i = 1; $i <= self::NB_VENTES; $i++) {
            // On ne crée plus de ventes s'il n'y a plus de stock du tout
            $produitsAvecStock = array_filter($produits, fn (Produit $p) => $p->getQuantiteStock() > 0);
            if (count($produitsAvecStock) === 0) {
                break;
            }

            $vente = new Vente();
            $vente->setDateVente(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $vente->setEtat($faker->randomElement(['En cours', 'Terminée']));
            $vente->setDateCreation(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $vente->setDateModification(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));

            /** @var Client $randomClient */
            $randomClient = $this->getReference('client' . rand(1, 4), Client::class);
            $vente->setClient($randomClient);

            $totalTtc = 0.0;

            // 1 à 3 lignes par vente
            $nbLignes = $faker->numberBetween(1, 3);
            for ($l = 1; $l <= $nbLignes; $l++) {
                $produitsAvecStock = array_filter($produits, fn (Produit $p) => $p->getQuantiteStock() > 0);
                if (count($produitsAvecStock) === 0) {
                    break;
                }

                /** @var Produit $produit */
                $produit = $faker->randomElement($produitsAvecStock);

                $stockActuel = $produit->getQuantiteStock();
                if ($stockActuel <= 0) {
                    continue;
                }

                // On vend au max 10 unités ou le stock actuel
                $maxQte = min(10, $stockActuel);
                $qte = $faker->numberBetween(1, $maxQte);

                $prixVente = (float) $produit->getPrixUnitaire();

                $detail = new DetailVente();
                $detail->setProduit($produit);
                $detail->setVente($vente);
                $detail->setQuantite($qte);
                $detail->setPrixUnitaire($prixVente);

                // calcul HT + TVA
                $ligneHt = $qte * $prixVente;
                $tauxTva = $this->normaliseTaux($produit->getTva());
                $ligneTtc = $ligneHt * (1 + $tauxTva);

                // Sous-total HT
                $detail->setSousTotal(number_format($ligneHt, 2, '.', ''));

                $totalTtc += $ligneTtc;

                // On réduit le stock du produit
                $produit->setQuantiteStock($stockActuel - $qte);

                $manager->persist($detail);
                $vente->addDetailVente($detail);
            }

            // Si la vente n'a pas de lignes (ex. plus de stock), on la saute
            if (count($vente->getDetailVentes()) === 0) {
                continue;
            }

            $vente->setMontantTotal(number_format($totalTtc, 2, '.', ''));

            $manager->persist($vente);
            $this->addReference('vente' . $i, $vente);
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
        // On a besoin des clients et des produits (déjà alimentés par les achats)
        return [
            ClientFixtures::class,
            ProduitFixtures::class,
            AchatFixtures::class,
        ];
    }
}
