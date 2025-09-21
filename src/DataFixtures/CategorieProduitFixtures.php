<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\CategorieProduit;

class CategorieProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $c = new CategorieProduit();
            $c->setNom($faker->word())
              ->setDescription($faker->sentence(10))
              ->setDateCreation(new \DateTimeImmutable())
              ->setDateModification(new \DateTimeImmutable());

            $manager->persist($c);
            $this->addReference('categorieProduit'.$i, $c);
        }
        $manager->flush();
    }
}
