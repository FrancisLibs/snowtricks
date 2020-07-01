<?php

namespace App\Controller;

use App\Entity\Trick;
use Twig\Environment;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
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
    public function index(TrickRepository $repository, int $nbTricks = 0, EntityManagerInterface $manager): Response
    {
        $nbTricksCount = $repository->countAll();
        $restTricks = $nbTricksCount - $nbTricks;

        $tricksToRead = $nbTricks + 4;
        // Mise à zéro de la variable page si plus de tricks à afficher
        if ($restTricks > 4) {
            $displayBtn = true;
            $nbTricks = $nbTricks + 4;
        } else {
            $displayBtn = false;
            $nbTricks = $nbTricks + $restTricks;
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
    public function moreTricks(TrickRepository $repository, Request $request, EntityManagerInterface $manager): Response
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
        $i = 0;
        if ($tricks) {
            foreach ($tricks as $trick) {
                $tricksArray[$i]['id'] = $trick->getId();
                $tricksArray[$i]['name'] = $trick->getName();
                $tricksArray[$i]['slug'] = $trick->getSlug();
                $i++;
            }
        }
        $results = array(
            'tricks'      => $tricksArray,
            'displayBtn'    =>  $displayBtn,
        );

        return new Response(json_encode($results));
    }
}
