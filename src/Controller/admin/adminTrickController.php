<?php 

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(TrickRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/tricks/delete{id}", name="trick.delete")
     */
    public function delete(Trick $trick)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trick);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/admin/tricks/edit/{id}", name="trick.edit")
     */
    public function edit()
    {

    }


}