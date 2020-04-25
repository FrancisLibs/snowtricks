<?php

namespace App\Controller;

use Twig\Environment;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
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
     */
    public function index(TrickRepository $repository): Response
    {
        $tricks = $repository->findAll();
        return new Response($this->twig->render('pages/home.html.twig', [
            'tricks'    =>   $tricks
        ]));
    }
}

