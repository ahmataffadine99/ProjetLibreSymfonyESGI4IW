<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = ['Science-fiction', 'Fantasy', 'Policier', 'Rock', 'Pop', 'Indie', 'Action', 'RPG', 'Simulation'];

        foreach ($tags as $nomTag) {
            $tag = new Tag();
            $tag->setNom($nomTag);
            $manager->persist($tag);
            $this->addReference('tag-' . strtolower(str_replace(' ', '-', $nomTag)), $tag);
        }

        $manager->flush();
    }
}