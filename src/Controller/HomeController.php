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
     * @Route("/home/trick/{nbTricks}", name="tricks.index")
     * @return Response
     */
    public function index(TrickRepository $repository, int $nbTricks = 0): Response
    {
        $nbTricksCount = $repository->countAll();
        $restTricks = $nbTricksCount - $nbTricks;

        $tricksToRead = $nbTricks + 4;
        // Mise à zéro de la variable page si plus de tricks à afficher
        $displayBtn = false;
        $nbTricks = $nbTricks + $restTricks;

        if ($restTricks > 4) {
            $displayBtn = true;
            $nbTricks = $nbTricks + 4;
        } 
        
        $tricks = $repository->findBy([], [], $tricksToRead, 0);
        
        return $this->render('pages/home.html.twig', [
            'tricks'        =>  $tricks,
            'displayBtn'    =>  $displayBtn,
            'nbTricks'      =>  $nbTricks
        ]);
    }

    /**
     * @Route("/tricks/home/ajax", name="home.tricks.more")
     * @param $page_var
     * @return Response
     */
    public function moreTricks(TrickRepository $repository, Request $request): Response
    {
        $nbTricks = $repository->countAll();
        $tricksDisplaying = $request->query->get('nbTricks');
        $tricksToDisplay = $nbTricks - $tricksDisplaying;
        $tricks = $repository->findBy([], [], 4, $tricksDisplaying);

        // Mise à zéro de la variable page si plus de tricks à afficher
        if ($tricksToDisplay >= 5) {
            $displayBtn = true;
        } else {
            $displayBtn = false;
        }

        $tricksArray = array();
        $counter = 0;
        if ($tricks) {
            foreach ($tricks as $trick) {
                $tricksArray[$counter]['id'] = $trick->getId();
                $tricksArray[$counter]['name'] = $trick->getName();
                $tricksArray[$counter]['slug'] = $trick->getSlug();
                $counter++;
            }
        }
        $results = array(
            'tricks'      => $tricksArray,
            'displayBtn'    =>  $displayBtn,
        );

        return new Response(json_encode($results));
    }
}
