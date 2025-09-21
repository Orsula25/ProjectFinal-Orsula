<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Vente;
use App\Entity\Client;
use Faker\Factory;
use App\DataFixtures\ClientFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VenteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        // $product = new Product();
        for ($i=1; $i <= 10; $i++) {
        $vente = new Vente();
        $vente->setDateVente(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
        $vente->setMontantTotal($faker->randomFloat(2, 100, 1000));
        $vente->setEtat($faker->randomElement(['En cours', 'Terminée']));
        $vente->setDateCreation(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
        $vente->setDateModification(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
 

        $this->addReference('vente' . $i, $vente);

        // lien avec un client aleatoire 
        /** @var Client $randomClient */
        $randomClient = $this -> getReference('client' . rand(1,4), Client::class);
        $vente -> setClient($randomClient);
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
