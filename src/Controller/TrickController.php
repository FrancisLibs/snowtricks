<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\UploadType;
use App\Form\CommentType;
use Cocur\Slugify\Slugify;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;


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
     * @Route("/trick/{slug}-{id}/{nbComments}", name="trick.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Trick $trick
     * @param page
     * @return Response
     */
    public function show(
        Trick $trick,
        string $slug,
        CommentRepository $commentRepository,
        EntityManagerInterface $manager,
        Request $request
    ): Response {
        if ($trick->getSlug() !== $slug) {
            return $this->redirectToRoute(
                'trick.show',
                [
                    'id'    =>  $trick->getId(),
                    'slug'  =>  $trick->getSlug()
                ],
                301
            );
        }

        

        $comment = new Comment();
        $form2 = $this->createForm(CommentType::class, $comment);

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->IsValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick.show', ['slug' => $trick->getSlug(), 'id' => $trick->getId()]);
        }

        // Affichage de 3 commentaires 
        $nbComments = $commentRepository->countTrikComments($trick);
        $comments = $commentRepository->findPaginateComments($trick, 0, 3);

        if ($nbComments <= 3) {
            $nbComments = 0;
        } else {
            $nbComments = 3;
        }

        return $this->render('trick/show.html.twig', [
            'current-menu'  =>  'tricks',
            'trick'         =>  $trick,
            'comments'      =>  $comments,
            'form'          =>  $form->createView(),
            'nbComments'    =>  $nbComments,
        ]);
    }

    /**
     * @Route("/trick/comments/more/{id}/{nbComments}", name="comment.more")
     */
    public function commentMore(Trick $trick, int $nbComments, CommentRepository $commentRepository, Request $request)
    {
        $comments = $commentRepository->findPaginateComments($trick, $nbComments, $nbComments);
        $nbCommentaires = $commentRepository->countTrikComments($trick);

        if ($nbCommentaires <= $nbComments + 1) {
            $nbComments = 0;
        }

        $commentArray = array();
        $i = 0;
        if ($comments) {
            foreach ($comments as $comment) {
                $commentArray[$i]['id'] = $comment->getId();
                $commentArray[$i]['content'] = $comment->getContent();
                $commentArray[$i]['date'] = $comment->getCreatedAt()->format('m-d-Y');
                $commentArray[$i]['heure'] = $comment->getCreatedAt()->format('g\hi');
                $commentArray[$i]['nbCommentaires'] = $nbComments;
                $i++;
            }
        }
        $results = array(
            'comments'      => $commentArray,
        );

        return new Response(json_encode($results));
    }
}
