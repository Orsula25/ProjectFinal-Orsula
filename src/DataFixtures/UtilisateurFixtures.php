<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Utilisateur;
use Faker\Factory;
use App\Entity\Entreprise;

class UtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // récupérer l’entreprise créée dans EntrepriseFixtures
       $entreprise = $this->getReference(EntrepriseFixtures::ENTREPRISE_DEMO, \App\Entity\Entreprise::class);

        // 10 users simples
        for ($i = 0; $i < 10; $i++) {
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail("user{$i}@gmail.com");
            $utilisateur->setNom("user{$i}");
            $utilisateur->setDateNaissace($faker->dateTimeBetween('-100 years', '-18 years'));
            $utilisateur->setRoles(['ROLE_USER']);
            $utilisateur->setPassword(
                $this->hasher->hashPassword($utilisateur, "Lepassword{$i}")
            );

            // lier à l’entreprise
            $utilisateur->setEntreprise($entreprise);

            $manager->persist($utilisateur);
        }

        // 5 admins
        for ($i = 0; $i < 5; $i++) {
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail("admin{$i}@gmail.com");
            $utilisateur->setNom("admin{$i}");
            $utilisateur->setDateNaissace($faker->dateTimeBetween('-100 years', '-18 years'));
            $utilisateur->setRoles(['ROLE_ADMIN']);
            $utilisateur->setPassword(
                $this->hasher->hashPassword($utilisateur, "Lepassword{$i}")
            );

            // eux aussi liés à la même entreprise
            $utilisateur->setEntreprise($entreprise);

            $manager->persist($utilisateur);
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EntrepriseFixtures::class,
        ];
    }
}
