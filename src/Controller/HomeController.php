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
     * @Route("/home/tricks/{page_var}", name="tricks.more")
     */
    public function index(TrickRepository $repository, $page_var = 1): Response
    {
        $tricks = $repository->findBy([], [], $page_var*4, 0);

        foreach($tricks as $trick)
        {
            // Première image de la liste d'images
            $pictures=$trick->getPictures();
            $picture = $pictures->first();
            $pictures->clear();
            $trick->addPicture($picture);
        }

        $nbTricks = $repository->countAll();
        if( $nbTricks > ($page_var * 4) )
        {
            $page_var ++;
        }
        else
        {
            $page_var = 0;
        }
        
        return new Response($this->twig->render('pages/home.html.twig', [
            'tricks'    =>  $tricks,
            'page'      =>  $page_var,
        ]));
    }
}

