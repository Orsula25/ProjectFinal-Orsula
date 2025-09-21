<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Fournisseur;
use DateTimeImmutable;

final class FournisseurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $f = (new Fournisseur())
                ->setNom($faker->company())
                ->setEmail($faker->companyEmail())
                ->setAdresse($faker->address())
                ->setTelephone($faker->phoneNumber())
                ->setNumTva($faker->randomNumber(9))
                ->setDateDerniereCommande(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')))
                ->setDateModification(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')))
                ->setDateCreation(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));

            $manager->persist($f);
            $this->addReference('fournisseur'.$i, $f);
        }
        $manager->flush();
    }
}