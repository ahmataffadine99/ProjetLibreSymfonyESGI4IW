<?php

namespace App\Tests\Unit;

use App\Entity\Objet;
use PHPUnit\Framework\TestCase;

class ObjetTest extends TestCase
{
    public function testObjetEstValide(): void
    {
        $objetValide = new Objet();
        $objetValide->setNom('Figurine de collection');
        $objetValide->setCategorie('Figurines');
        $this->assertTrue($objetValide->estValide());

        $objetSansNom = new Objet();
        $objetSansNom->setCategorie('Figurines');
        $this->assertFalse($objetSansNom->estValide());

        $objetSansCategorie = new Objet();
        $objetSansCategorie->setNom('Figurine de collection');
        $this->assertFalse($objetSansCategorie->estValide());

        $objetInvalide = new Objet();
        $this->assertFalse($objetInvalide->estValide());
    }
}