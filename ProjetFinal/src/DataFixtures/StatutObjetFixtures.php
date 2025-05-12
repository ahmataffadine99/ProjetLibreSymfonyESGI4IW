<?php

namespace App\DataFixtures;

use App\Entity\StatutObjet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatutObjetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuts = ['Neuf', 'Occasion', 'Bon état', 'Mauvais état', 'Prêté'];

        foreach ($statuts as $nomStatut) {
            $statut = new StatutObjet();
            $statut->setNom($nomStatut);
            $manager->persist($statut);
            $this->addReference('statut-' . strtolower(str_replace(' ', '-', $nomStatut)), $statut);
        }

        $manager->flush();
    }
}