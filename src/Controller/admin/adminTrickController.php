<?php 

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/admin/trick/create", name="admin.trick.new")
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();
            return $this->redirectToRoute('tricks.index');
        }

        return $this->render('admin/trick/new.html.twig', [
            'trick'     =>  $trick,
            'form'      =>  $form->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/{id}", name="admin.trick.edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function edit(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('tricks.index');
        }


        return $this->render('admin/trick/edit.html.twig', [
            'trick'     =>  $trick,
            'form'      =>  $form->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/{id}", name="admin.trick.delete", methods="DELETE")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Trick $trick, Request $request, Trick $tricks)
    {
        

        if($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token')))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

            return $this->redirectToRoute('tricks.index');
        }

        return $this->redirectToRoute('pages/home.html.twig');
    }
}