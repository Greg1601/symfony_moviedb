<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\Slugger;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    public function testSlugify()
    {
        //Tests avec minuscule demandé

        //par defaut mon slugger à la propriété to lower au niveau du constructeur
        $slugger = new Slugger();

        //je teste si la chaine passée a mon slugger est bien celle attendue
        $result1 = $slugger->slugify('Hello World');
        $result2 = $slugger->slugify('un éléphant ca Trompe enormemenT');

        $this->assertEquals('hello-world', $result1);
        $this->assertEquals('un-l-phant-ca-trompe-enormement', $result2);

         //Tests avec majuscule autorisé

        $slugger = new Slugger(false);

        $result1 = $slugger->slugify('Hello World');
        $result2 = $slugger->slugify('un éléphant ca Trompe enormemenT');

        $this->assertEquals('Hello-World', $result1);
        $this->assertEquals('un-l-phant-ca-Trompe-enormemenT', $result2);
    }
}