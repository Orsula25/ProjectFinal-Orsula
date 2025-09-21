<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Produit;
use App\Entity\CategorieProduit;

final class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $produit = new Produit();
            $produit->setNom(ucfirst($faker->words(5, true)));
            $produit->setDescription($faker->paragraph());
            $produit->setPrixUnitaire($faker->randomFloat(2, 10, 1000));
            $produit->setQuantiteStock($faker->numberBetween(0, 100));
            $produit->setReference(sprintf('REF-%s-%04d', strtoupper($faker->lexify('????')), $i));
            $produit->setTva($faker->randomElement([5.5, 10, 20]));

            /** @var CategorieProduit $cat */
            $cat = $this->getReference('categorieProduit' . rand(1, 10), CategorieProduit::class);
            $produit->setCategorieProduit($cat);

            $manager->persist($produit);
            $this->addReference('produit' . $i, $produit);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategorieProduitFixtures::class];
    }
}
