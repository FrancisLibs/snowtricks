<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\MainPictureType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TrickController extends AbstractController
{
    /**
     * @Route("/trick/{slug}-{id}/{nbComments=0}", name="trick.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Trick $trick
     * @param string $slug
     * @param int $nbComments
     * @return Response
     */
    public function trickShow( Trick $trick, string $slug, int $nbComments = 0, CommentRepository $commentRepository,
        EntityManagerInterface $manager, Request $request): Response 
    {
        if ($trick->getSlug() !== $slug) {
            return $this->redirectToRoute('trick.show', [
                    'id'    =>  $trick->getId(),
                    'slug'  =>  $trick->getSlug()
                ],
                301
            );
        }

        // Modification de l'image à la une
        $form1 = $this->createForm(MainPictureType::class, $trick);
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) 
        {            
            $manager->flush();

            return $this->redirectToRoute('trick.show', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
                'nbComments' => $nbComments,
            ]);
        }

        $comment = new Comment();
        $form2 = $this->createForm(CommentType::class, $comment);

        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->IsValid()) 
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick.show', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
                'nbComments' => $nbComments,
            ]);
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
            'trick'         =>  $trick,
            'comments'      =>  $comments,
            'form1'         =>  $form1->createView(),
            'form2'         =>  $form2->createView(),
            'current-menu'  =>  'tricks',
            'nbComments'    =>  $nbComments,
            
        ]);
    }

    /**
     * @Route("/trick/comments/more/{id}/{nbComments}", name="comment.more")
     * 
     */
    public function commentMore(Trick $trick, int $nbComments, CommentRepository $commentRepository, Request $request)
    {
        $nbComments = $commentRepository->countTrikComments($trick);
        $commentsDisplaying = $request->query->get('nbCommentaires');
        $comments = $commentRepository->findPaginateComments($trick, $commentsDisplaying, 1);


        if ($nbComments >= $commentsDisplaying + 2) {
            $displayBtn = true;
        } else {
            $displayBtn = false;
        }

        $commentArray = array();
        $i = 0;
        if ($comments) {
            foreach ($comments as $comment) {
                $commentArray[$i]['id'] = $comment->getId();
                $commentArray[$i]['content'] = $comment->getContent();
                $commentArray[$i]['date'] = $comment->getCreatedAt()->format('m-d-Y');
                $commentArray[$i]['heure'] = $comment->getCreatedAt()->format('g\hi');
                $commentArray[$i]['displayBtn'] = $displayBtn;
                $i++;
            }
        }
        $results = array(
            'comments'      => $commentArray,
        );

        return new Response(json_encode($results));
    }
}
