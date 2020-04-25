<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\DataFixtures\TricksFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i =1; $i<=10; $i++)
        {
            $trick = new Trick();
            $trick
                ->setName('numéro'. $i)
                ->setDescription('Ceci est la descritpion du trick n°' . $i);

            $manager->persist($trick);
        }        

        $manager->flush();
    }
}
