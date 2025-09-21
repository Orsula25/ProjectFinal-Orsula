<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Client;
use Faker\Factory;

class ClientFixtures extends Fixture
{
    private const NB_CLIENTS = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= self::NB_CLIENTS; $i++) {
            $client = new Client();
            $client->setNom($faker->firstName());
            $client->setPrenom($faker->lastName());
            $client->setEmail($faker->email());
            $client->setTelephone($faker->phoneNumber());
            $client->setAdresse($faker->address());
            $client->setDateCreation(new \DateTimeImmutable());
            $client->setDateModification(new \DateTimeImmutable());

            $manager->persist($client);
            $this->addReference('client' . $i, $client);
        }

        $manager->flush();
    }


}
