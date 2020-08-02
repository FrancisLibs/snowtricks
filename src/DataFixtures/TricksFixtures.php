<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

        //Creation of 5 users
        $users = array('marc', 'jean', 'eric', 'sophie', 'marie');
        foreach ($users as $username) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
            $user->setEmail($faker->email);
            $index = rand(1, 11);
            $user->setUserPicture('profil'.$index.'.jpg');
            $manager->persist($user);

            // Creation des catégories
            $categories = array('flips', 'grabs', 'spins', 'slides', 'rotations', 'oneFoot');
            foreach ($categories as $categ) {
                $category = new Category();
                $category->setName($categ);
                $manager->persist($category);
        
                // A chaque catégorie on met de 1 à 3 tricks
                $tricksNames = array('FrontFlip', 'BackFlip', '360', 'Reverso', 'Regular', 'Goofy', 'graber', 'Backside air',
                    'Mute japan', 'Stalefish', 'Indy Nosebone', 'Tailgrab');
                
                for($cptTricks =1; $cptTricks<=mt_rand(1, 3); $cptTricks++)
                {
                    $descript = "<p>" . join("</p><p>", $faker->paragraphs(3)) . "</p>";
        
                    $trick = new Trick();
                    $index = rand(1, 10);
                    $trick  ->setName($tricksNames[array_rand($tricksNames, 1)])
                            ->setDescription($descript)
                            ->setCreatedAt($faker->dateTimeBetween('-100 days'))
                            ->setUpdatedAt($faker->dateTimeBetween('-6 months'))
                            ->setCategory($category)
                            ->setUser($user)
                            ->setMainFileName('main'. $index.'.jpg');

                    // ajout de 2 à 5 photos
                    for ($cptPictures = 1; $cptPictures <= mt_rand(2, 5); $cptPictures++)
                    {
                        $picture = new Picture();
                        $index = rand(1, 5);
                        $picture->setFilename($categ.$index . '.jpg')
                                ->setTrick($trick);                        
                        $trick->addPicture($picture);

                        $manager->persist($picture);
                    }

                    // ajout de 1 à 3 videos
                    $videos=array('https://www.youtube.com/embed/W853WVF5AqI', 'https://www.youtube.com/embed/euDhH_5hP0w',
                    'https://www.youtube.com/embed/jTEQUyruKfE', 'https://www.youtube.com/embed/L4bIunv8fHM',
                    'https://www.youtube.com/embed/ydGgOxmYEgs', 'https://www.youtube.com/embed/CA5bURVJ5zk',
                    'https://www.youtube.com/embed/DfvI-Q6KUe8', 'https://www.youtube.com/embed/mTFMakbP0xw',
                    'https://www.youtube.com/embed/CGUhvqKlWsE', 'https://www.youtube.com/embed/4IVdWdvsrVA',
                    'https://www.youtube.com/embed/d7dpo_G9npo', 'https://www.youtube.com/embed/qFanNTiY6-0');

                    for ($cptVideos = 1; $cptVideos <= mt_rand(1, 3); $cptVideos++) {
                        $video = new Video();
                        $randKey = array_rand($videos, 1);
                        $video  ->setLink($videos[$randKey])
                                ->setTrick($trick);
                        $trick->addVideo($video);

                        $manager->persist($video);
                    }

                    // Ajout entre 2 et 5 commentaires
                    for($k = 1; $k <= mt_rand(2, 5); $k++)
                    {
                        $days = (new \DateTime())->diff($trick->getCreatedAt())->days;
                        $content = "<p>" . join("</p><p>", $faker->paragraphs(1)) . "</p>";

                        $comment = new Comment();

                        $comment->setContent($content)
                                ->setCreatedAt($faker->dateTimeBetween('-' . $days .'days'))
                                ->setTrick($trick)
                                ->setUser($user);
                        $trick->addComment($comment);

                        $manager->persist($comment);
                    }

                    $manager->persist($trick);
                    
                }
            }
        }
        $manager->flush();        
    }
}
