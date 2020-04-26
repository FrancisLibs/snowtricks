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
        $faker = \Faker\Factory ::create('fr_FR');

        for($i =1; $i<=10; $i++)
        {
            $descript = "<p>" . join("</p><p>", $faker->paragraphs) . "</p>";
            $trick = new Trick();
            $trick
                ->setName($faker->name)
                ->setDescription($descript)
                ->setCreatedAt($faker->dateTimeBetween('-100 days'))
                ->setModificationAt($faker->dateTimeBetween('-100 days'));
            $manager->persist($trick);
        }        
        $manager->flush();
    }
}
