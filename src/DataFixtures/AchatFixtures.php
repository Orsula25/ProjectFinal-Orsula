<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Achat;
use App\Entity\Fournisseur;
use Faker\Factory;
use DateTimeImmutable;

final class AchatFixtures extends Fixture implements DependentFixtureInterface
{
    private const NB_ACHATS = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= self::NB_ACHATS; $i++) {
            $achat = new Achat();
            $achat->setDateAchat(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $achat->setMontantTotal($faker->randomFloat(2, 100, 2000));
            $achat->setEtat($faker->randomElement(['en_cours', 'termine']));
            $achat->setDateCreation(new DateTimeImmutable());
            $achat->setDateModification(new DateTimeImmutable());

            /** @var Fournisseur $fournisseur */
            $fournisseur = $this->getReference('fournisseur' . rand(1, 10), Fournisseur::class);
            $achat->setFournisseur($fournisseur);

            $manager->persist($achat);
            $this->addReference('achat' . $i, $achat);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [FournisseurFixtures::class];
    }
}
