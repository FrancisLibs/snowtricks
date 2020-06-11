<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="home")
     * @Route("/home/trick/{page_var}", name="tricks.index")
     * @param $page_var
     * @return Response
     */
    public function index(TrickRepository $repository, $page_var = 1, EntityManagerInterface $manager): Response
    {
        $tricks = $repository->findBy([], [], $page_var * 4, 0);

        //Image à la une...
        foreach ($tricks as $trick) {
            $pictures = $trick->getPictures();
            $mainPicture = $trick->getMainPicture();

            if (empty($mainPicture)) {
                if ($pictures->isEmpty()) {
                    $trick->setMainPicture('build/empty.jpg');
                } else {
                    $mainPicture = $pictures->first()->getFile();
                    $trick->setMainPicture($mainPicture);
                }
            }

            $manager->persist($trick);
        }

        $manager->flush();

        // Mise à zéro de la variable page si plus de tricks à afficher
        $nbTricks = $repository->countAll();
        if ($nbTricks > ($page_var * 4)) {
            $page_var++;
        } else {
            $page_var = 0;
        }

        return new Response($this->twig->render('pages/home.html.twig', [
            'tricks'        =>  $tricks,
            'page'          =>  $page_var,
        ]));
    }
}
