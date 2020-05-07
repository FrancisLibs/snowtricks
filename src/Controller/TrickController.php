<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
    public function show(Request $request, Trick $trick, string $slug): Response
    {
        if($trick->getSlug() !== $slug)
        {
            return $this->redirectToRoute('trick.show', [
                'id'    =>  $trick->getId(),
                'slug'  =>  $trick->getSlug()
            ],
            301);
        }
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->IsValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick.show', ['slug'=>$trick->getSlug(), 'id' => $trick->getId()]);
        }
        

        return $this->render('trick/show.html.twig', [
            'trick'         =>  $trick,
            'current-menu'  =>  'tricks',
            'form'          =>  $form->createView()
        ]);
    }
}