<?php

namespace App\DataFixtures;

use App\Entity\Emplacement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmplacementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $emplacements = ['Étagère A', 'Boîte 12', 'Vitrine Salon', 'Bureau Tiroir du haut'];

        foreach ($emplacements as $nomEmplacement) {
            $emplacement = new Emplacement();
            $emplacement->setNom($nomEmplacement);
            $manager->persist($emplacement);
            $this->addReference('emplacement-' . strtolower(str_replace(' ', '-', $nomEmplacement)), $emplacement);
        }

        $manager->flush();
    }
}