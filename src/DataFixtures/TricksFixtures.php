<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Category;
use App\DataFixtures\TricksFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory ::create('fr_FR');

        // Tableau catégories
        $categories = array('Straight airs', 'Grabs', 'Spins', 'Flips and inverted rotations', 'Inverted hand plants', 'Slides', 'Stalls', 'Tweaks and variations', 'Miscellaneous tricks and identifiers');
        foreach($categories as $categ)
        {
            $category = new Category();

            $category->setName($categ);

            $manager->persist($category);

            // Créer entre 2 et 4 tricks
            for($j =1; $j<=mt_rand(1, 3); $j++)
            {
                $descript = "<p>" . join("</p><p>", $faker->paragraphs(3)) . "</p>";

                $trick = new Trick();

                $trick  ->setName($faker->name)
                        ->setDescription($descript)
                        ->setCreatedAt($faker->dateTimeBetween('-100 days'))
                        ->setModificationAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
                $manager->persist($trick);

                // Ajout entre 1 et 4 commentaires
                for($k = 1; $k <= mt_rand(1, 4); $k++)
                {
                    $days = (new \DateTime())->diff($trick->getCreatedAt())->days;
                    $content = "<p>" . join("</p><p>", $faker->paragraphs(1)) . "</p>";

                    $comment = new Comment();

                    $comment->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days .'days'))
                            ->setTrick($trick);
                    $manager->persist($comment);
                }

                // Ajout entre 1 et 5 images
                for($l = 1; $l <= mt_rand(1, 5); $l++)
                {
                    $picture = new Picture();
                    
                    $picture->setName($faker->word)
                            ->setUrl($faker->imageUrl(500, 500))
                            ->setCreatedAt($faker->dateTimeBetween('-100 days'))
                            ->setTrick($trick);
                    $manager->persist($picture);
                }
            }
        }        
        $manager->flush();
    }
}
