<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\DetailAchat;
use App\Entity\Achat;
use App\Entity\Produit;

final class DetailAchatFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            /** @var Achat $achat */     $achat   = $this->getReference('achat'.rand(1, 10), Achat::class);     // ⇦ adapte 10
            /** @var Produit $produit */ $produit = $this->getReference('produit'.rand(1, 10), Produit::class); // ⇦ adapte 10

            $qte = $faker->numberBetween(1, 10);
            $pu  = $faker->randomFloat(2, 1, 100);

            $da = (new DetailAchat())
                ->setAchat($achat)
                ->setProduit($produit)
                ->setQuantite($qte)
                ->setPrixUnitaire($pu)
                ->setSousTotal($qte * $pu);

            $manager->persist($da);
            $this->addReference('detailAchat' . $i, $da);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AchatFixtures::class, ProduitFixtures::class];
    }
}