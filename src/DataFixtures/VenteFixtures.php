<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Vente;
use App\DataFixtures\ClientFixtures;
use Faker\Factory;

class VenteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // on génère par 15 ventes 
        for ($i = 1; $i <= 15; $i++) {
        // $product = new Product();
        $vente = new Vente();
        $vente->setDateVente(\DateTimeImmutable::createFromMutable(
            $faker->dateTimeBetween('-1 year', 'now')
        ));
        $vente->setMontantTotal($faker->randomFloat(2, 100, 1000));
        $vente->setEtat($faker->randomElement(['En cours', 'Terminée', 'Annulée']));
        
        // Associer la vente à un client aléatoire (parmi les 5 créés dans ClientFixtures)
        $randomClientIndex = $faker->numberBetween(1, 5);
        $vente->setClient(
            $this->getReference('client'.$randomClientIndex),
            
        );
        
        $manager->persist($vente);

    }
        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }
}



