<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Entreprise;


class EntrepriseFixtures extends Fixture
{
    public const ENTREPRISE_DEMO = 'entreprise-demo';
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        $e = new Entreprise();
        $e->setNom('Mon Petit Magasin');
        $e->setAdresse('Rue du Commerce 1, 1000 Bruxelles');
        $e->setTelephone('0123 45 67 89');
        $e->setEmail('contact@monpetitmagasin.test');
        $e->setNumTva('BE0123.456.789');
        // $manager->persist($product);
        $manager->persist($e);

        $manager->flush();

        // Pour la réutiliser dans d’autres fixtures
        $this->addReference(self::ENTREPRISE_DEMO, $e);
    }
}
