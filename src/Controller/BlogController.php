<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/mon", name="mon")
     */
    public function index()
    {
        return $this->render('mon/index.html.twig', [
            'controller_name' => 'MonController',
        ]);
    }

    /**
     * @Route("/mon", name="mon")
     */
    public function show()
    {
        return $this->render('mon/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
}
