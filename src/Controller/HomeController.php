<?php

namespace App\Controller;

use App\Entity\Trick;
use Twig\Environment;
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
     * @Route("/home/tricks/{page_var}", name="tricks.more")
     * @return Response
     */
    public function index(TrickRepository $repository, $page_var = 1): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $tricks = $repository->findBy([], [], $page_var*4, 0);

        foreach($tricks as $trick)
        {
            if($trick->getMainPicture() == NULL)
            {
                $pictures=$trick->getPictures();
                $trick->setMainPicture($pictures->first());// Première image de la liste d'images

                $entityManager->persist($trick);
                $entityManager->flush();
            }
        }
        
        // Mise à zéro de la variable page si plus de tricks à afficher
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

