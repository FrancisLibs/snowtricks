<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SnowController extends AbstractController
{
    /**
     * @Route("/snow", name="snow")
     */
    public function index()
    {
        return $this->render('snow/index.html.twig', [
            'controller_name' => 'SnowController',
        ]);
    }
}
