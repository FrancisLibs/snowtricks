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
use Symfony\Component\Security\Core\User\UserInterface;
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
        EntityManagerInterface $manager, Request $request, ?UserInterface $user): Response 
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
            $comment->setUser($user);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick.show', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
                'nbComments' => $nbComments,
            ]);
        }

        // Affichage de 3 commentaires 
        $nbComments = $commentRepository->countTrikComments($trick);
        $comments = $commentRepository->findPaginateComments($trick, 0, 3);

        $displayButton = true;
        if ($nbComments <= 3) {
            $displayButton = false;
        }

        if ($nbComments > 3) {$nbComments = 3;}

        return $this->render('trick/show.html.twig', [
            'trick'         =>  $trick,
            'comments'      =>  $comments,
            'form1'         =>  $form1->createView(),
            'form2'         =>  $form2->createView(),
            'current-menu'  =>  'tricks',
            'nbComments'    =>  $nbComments,
            'buttonMore'    =>  $displayButton,
            
        ]);
    }

    /**
     * @Route("/trick/comments/more/{id}/{nbComments}", name="comment.more")
     * 
     */
    public function commentMore(Trick $trick, int $nbComments, CommentRepository $commentRepository)
    {
       
        $nbTrickComments = $commentRepository->countTrikComments($trick);
        
        $comment = $commentRepository->findOnePaginateComment($trick, $nbComments);

        $nbComments++;
       
        $displayButton = true;
        if($nbTrickComments == $nbComments)
        {
            $displayButton = false;
        }

        return $this->render('trick/_comments.html.twig', [
            'trick'         =>  $trick,
            'comment'       =>  $comment,
            'nbComments'    =>  $nbComments,
            'buttonMore'    =>  $displayButton,
        ]);
    }
}
