<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\ProduitFournisseur;
use App\Entity\Produit;
use App\Entity\Fournisseur;

final class ProduitFournisseurFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // ajuste 10/10 si tu as un autre volume
        for ($i = 1; $i <= 10; $i++) {
            /** @var Produit $p */     $p = $this->getReference('produit'.$i, Produit::class);
            /** @var Fournisseur $f */ $f = $this->getReference('fournisseur'.rand(1, 10), Fournisseur::class);

            $pf = (new ProduitFournisseur())
                ->setProduit($p)
                ->setFournisseur($f)
                ->setPrix($faker->randomFloat(2, 1, 100))
                ->setDelaiLivraison($faker->numberBetween(1, 10))
                ->setDateCreation(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')))
                ->setDateModification(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));

            $manager->persist($pf);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProduitFixtures::class, FournisseurFixtures::class];
    }
}