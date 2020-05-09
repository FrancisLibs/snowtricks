<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TrickController extends AbstractController
{
    /**
     * @Route("/tricks", name="tricks")
     * @return null
     */
    public function index()
    {
        return null;
    }

    /**
     * @Route("/tricks/{slug}-{id}", name="trick.show", requirements={"slug": "[a-z0-9\-]*"} )
     * @Route("/tricks/comments/{page_var}", name="comments.more")
     * @param Trick $trick
     * @param page_var
     * @return Response
     */
    public function show(Request $request, Trick $trick, string $slug, CommentRepository $repository, $page_var=1): Response
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

        // Pagination des commentaires
        $nbComments = $page_var * 1;
        $comments = $repository->findBy(['trick' => $trick], ['createdAt' => 'DESC'], $nbComments, 0);
        
        $page = $page_var;
        
        dump($comments);
        return $this->render('trick/show.html.twig', [
            'trick'         =>  $trick,
            'current-menu'  =>  'tricks',
            'form'          =>  $form->createView(),
            'page'          =>  $page,
            'comments'      =>  $comments
            ]);
    }
}