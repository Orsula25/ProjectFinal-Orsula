<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\DetailVente;
use App\Entity\Vente;
use App\Entity\Produit;
use App\Entity\Client;
use App\DataFixtures\VenteFixtures;
use App\DataFixtures\ProduitFixtures;
use App\DataFixtures\ClientFixtures;

class DetailVenteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //choisi un client aléatoire une vente et un produit existant 
     
       
        // $product = new Product();
        for ($i = 0; $i < 20; $i++) {
            $detailVente = new DetailVente();
            $detailVente->setQuantite($faker->numberBetween(1, 10));
            $detailVente->setPrixUnitaire($faker->randomFloat(2, 10, 100));
            $detailVente->setSousTotal($detailVente->getQuantite() * $detailVente->getPrixUnitaire());

            /** @var Vente $vente */
            $vente = $this->getReference('vente' . rand(1, 10), Vente::class); 
            /** @var Produit $produit */
            $produit = $this->getReference('produit' . rand(1, 10), Produit::class); 
            $detailVente->setVente($vente);
            $detailVente->setProduit($produit);
            $manager->persist($detailVente);


            
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            VenteFixtures::class,
            ProduitFixtures::class,
        ];
    }
}
