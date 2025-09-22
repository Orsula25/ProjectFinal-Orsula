<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Utilisateur;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Faker\Factory;


class UtilisateurFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $utlisateur = new Utilisateur();
            $utlisateur->setEmail("user" . $i . "@gmail.com");
            $utlisateur->setNom("user" . $i);
            $utlisateur->setDateNaissace($faker->dateTimeBetween('-100 years', '-18 years'));
            $utlisateur->setRoles(['ROLE_USER']);
            $utlisateur->setPassword($this ->hasher->hashPassword($utlisateur, "Lepassword" . $i));



            
            $manager->persist($utlisateur);
        }



        for ($i = 0; $i < 5; $i++) {
            $utlisateur = new Utilisateur();
            $utlisateur->setEmail("admin" . $i . "@gmail.com");
            $utlisateur->setNom("admin" . $i);
            $utlisateur->setDateNaissace($faker->dateTimeBetween('-100 years', '-18 years'));
            $utlisateur->setRoles(['ROLE_ADMIN']);
            $utlisateur->setPassword($this ->hasher->hashPassword($utlisateur, "password" . $i));
            $manager->persist($utlisateur);
        }
        
        $manager->flush();
    }
}
