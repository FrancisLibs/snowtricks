<?php

namespace App\Controller;

use App\Entity\Trick;
use Twig\Environment;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
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
     * @return Response
     */
    public function index(TrickRepository $repository, int $tricksDisplay = 0): Response
    {
        $nbTricks = $repository->countAll();
        $button = true;
        $tricksDisplay = 4;

        $tricks = $repository->findBy([], [], $tricksDisplay, 0 );

        if($nbTricks <= 4 )
        {
            $button = false;
        }

        return $this->render('pages/home.html.twig', [
            'tricks'    =>  $tricks,
            'button'    =>  $button,
            'tricksDisplay'   =>  $tricksDisplay,
        ]);
    }

    /**
     * @Route("/home/trick/more/{tricksDisplay}", name="tricks.index.more")
     * @return Response
     */
    public function indexMore(TrickRepository $repository, int $tricksDisplay = 0): Response
    {
        $nbTricks = $repository->countAll();
        $button = true;
        $tricksDisplay++;
        $tricks = $repository->findBy([], [], $tricksDisplay, 0 );

        if ($nbTricks <= $tricksDisplay ) {
            $button = false;
        }

        return $this->render('pages/home.html.twig', [
            'tricks'    =>  $tricks,
            'button'    =>  $button,
            'tricksDisplay'   =>  $tricksDisplay,
        ]);
    }
}
