<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TrickController extends AbstractController
{
    /**
     * @Route("/tricks", name="tricks")
     */
    public function index()
    {
        return null;
    }

    /**
     * @Route("/tricks/{slug}-{id}", name="trick.show", requirements={"slug": "[a-z0-9\-]*"} )
     * @param Trick $trick
     * @return Response
     */
    public function show(Trick $trick, string $slug): Response
    {
        if($trick->getSlug() !== $slug)
        {
            return $this->redirectToRoute('trick.show', [
                'id'    =>  $trick->getId(),
                'slug'  =>  $trick->getSlug()
            ],
            301);
        }
        return $this->render('trick/show.html.twig', [
            'trick'         =>  $trick,
            'current-menu'  =>  'tricks'
        ]);
    }
}