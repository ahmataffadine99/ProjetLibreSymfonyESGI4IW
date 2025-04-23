<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un administrateur
        $admin = new Utilisateur();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin123'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);
        $this->addReference('admin-user', $admin);

        // Création d'un modérateur
        $moderateur = new Utilisateur();
        $moderateur->setEmail('moderateur@example.com');
        $moderateur->setRoles(['ROLE_MODERATEUR']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $moderateur,
            'modo456'
        );
        $moderateur->setPassword($hashedPassword);
        $manager->persist($moderateur);
        $this->addReference('moderateur-user', $moderateur);

        // Création d'un utilisateur normal
        $user = new Utilisateur();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'user789'
        );
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $this->addReference('normal-user', $user);

        $manager->flush();
    }
}