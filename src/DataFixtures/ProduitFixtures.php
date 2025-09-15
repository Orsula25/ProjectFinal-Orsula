<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Produit;
use App\Entity\CategorieProduit;
use Faker\Factory;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création d'une catégorie par défaut si elle n'existe pas
        $categorie = new CategorieProduit();
        $categorie->setNom('Général');
        $categorie->setDescription('Catégorie par défaut');
        $manager->persist($categorie);

        for ($i = 0; $i < 3; $i++) {
            $produit = new Produit();
            $produit->setNom(ucfirst($faker->words(3, true)));
            $produit->setDescription($faker->paragraph());
            $produit->setPrixUnitaire($faker->randomFloat(2, 10, 1000));
            $produit->setQuantiteStock($faker->numberBetween(0, 100));
            $produit->setReference(sprintf('REF-%s-%04d', strtoupper($faker->lexify('????')), $i + 1));
            $produit->setCategorieProduit($categorie);
            $produit->setTva($faker->randomElement([5.5, 10, 20]));
            
            $manager->persist($produit);
            
            // Ajout d'une référence pour pouvoir l'utiliser dans d'autres fixtures
            $this->addReference('produit_' . $i, $produit);
        }

        $manager->flush();
    }
    
}
