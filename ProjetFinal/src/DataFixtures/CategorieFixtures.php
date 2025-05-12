<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['Livre de fiction', 'Livre documentaire', 'Album Pop', 'Album Rock', 'Jeu d\'aventure', 'Jeu de stratégie'];

        foreach ($categories as $nomCategorie) {
            $categorie = new Categorie();
            $categorie->setNom($nomCategorie);
            $manager->persist($categorie);
            $this->addReference('categorie-' . strtolower(str_replace(' ', '-', $nomCategorie)), $categorie);
        }

        $manager->flush();
    }
}