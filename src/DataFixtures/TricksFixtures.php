<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class TricksFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // Creation of the categories, so each categorie receive 5 users
        $categories = array('Straight airs', 'Grabs', 'Spins', 'Flips and inverted rotations', 
            'Inverted hand plants', 'Slides', 'Stalls', 'Tweaks and variations', 
            'Miscellaneous tricks and identifiers');
        foreach ($categories as $categ) 
        {
            $category = new Category();
            $category->setName($categ);
            $manager->persist($category);
        
            //Creation of 5 users
            for ($i = 0; $i < 4; $i++)
            {
                $user = new User();
                $user->setUsername($faker->name);
                $user->setPassword($this->passwordEncoder->encodePassword($user,'password'));
                $user->setEmail($faker->email);
                $manager->persist($user);
        
                // To each user belongs from 1 to 3 tricks
                for($j =1; $j<=mt_rand(1, 3); $j++)
                {
                    $descript = "<p>" . join("</p><p>", $faker->paragraphs(3)) . "</p>";
        
                    $trick = new Trick();
                    $trick  ->setName($faker->name)
                            ->setDescription($descript)
                            ->setCreatedAt($faker->dateTimeBetween('-100 days'))
                            ->setUpdatedAt($faker->dateTimeBetween('-6 months'))
                            ->setCategory($category)
                            ->setUser($user);

                    $manager->persist($trick);

                    // Ajout entre 1 et 5 commentaires
                    for($k = 1; $k <= mt_rand(1, 5); $k++)
                    {
                        $days = (new \DateTime())->diff($trick->getCreatedAt())->days;
                        $content = "<p>" . join("</p><p>", $faker->paragraphs(1)) . "</p>";

                        $comment = new Comment();

                        $comment->setContent($content)
                                ->setCreatedAt($faker->dateTimeBetween('-' . $days .'days'))
                                ->setTrick($trick);
                        $manager->persist($comment);
                    }
                }

            }
        }
        $manager->flush();        
    }
}
