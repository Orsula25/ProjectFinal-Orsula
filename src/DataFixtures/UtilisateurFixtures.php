<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Utilisateur;


class UtilisateurFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $utlisateur = new Utilisateur();
            $utlisateur->setEmail("user" .$i ."@gmail.com");
            // $utlisateur->setRoles();
            $utlisateur->setPassword($this ->hasher->hashPassword($utlisateur, "password" . $i));
            $manager->persist($utlisateur);
        }
        $manager->flush();
    }
}
