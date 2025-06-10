<?php
// tests/Unit/LivreTest.php

namespace App\Tests\Unit;

use App\Entity\Livre;
use PHPUnit\Framework\TestCase;

class LivreTest extends TestCase
{
    public function testGetFormattedTitleAndAuthor(): void
    {
        // 1. Arrange (Préparation) : Crée une instance de l'entité Livre
        $livre = new Livre();
        $livre->setNom("Le Seigneur des Anneaux");
        $livre->setAuteur("J.R.R. Tolkien");

        // 2. Act (Action) : Appelle la méthode que l'on veut tester
        $formattedString = $livre->getFormattedTitleAndAuthor();

        // 3. Assert (Assertion) : Vérifie que le résultat est conforme à l'attendu
        $this->assertEquals("Le Seigneur des Anneaux par J.R.R. Tolkien", $formattedString);
    }

    public function testGetFormattedTitleAndAuthorWithEmptyAuthor(): void
    {
        $livre = new Livre();
        $livre->setNom("Un livre sans auteur");
        $livre->setAuteur(""); // Auteur vide

        $formattedString = $livre->getFormattedTitleAndAuthor();

        $this->assertEquals("Un livre sans auteur par ", $formattedString);
    }
}