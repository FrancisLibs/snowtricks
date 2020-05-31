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
     * @Route("/trick/comments/{slug}-{id}/{nbComments}", name="trick.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Trick $trick
     * @param page
     * @return Response
     */
    public function show(Trick $trick, string $slug, int $nbComments = 3, CommentRepository $commentRepository, 
    EntityManagerInterface $manager,  Request $request): Response
    {
        if($trick->getSlug() !== $slug)
        {
            return $this->redirectToRoute('trick.show', [
                'id'    =>  $trick->getId(),
                'slug'  =>  $trick->getSlug()
            ],
            301);
        }
        // form 1
        $form1 = $this->createForm(UploadType::class);

        // form 2
        $comment = new Comment();
        $form2 = $this->createForm(CommentType::class, $comment);

        $form1->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) 
        {
            $pictureFile = $form1->get('file')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename =(new Slugify())->slugify($originalFilename);
                $newFilename = '/build/'.$safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                $pictureFile->move($this->getParameter('pictures_directory'), $newFilename);

                $trick->setMainPicture($newFilename);
                
                $manager->flush();
            }
            return $this->redirectToRoute('trick.show', ['slug'=>$trick->getSlug(), 'id'=>$trick->getId()]);
        }

        $form2->handleRequest($request);
        if($form2->isSubmitted() && $form2->IsValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick.show', ['slug'=>$trick->getSlug(), 'id'=>$trick->getId()]);
        }

        // Pagination des commentaires
        $comments= $commentRepository->findPaginateComments($trick, $nbComments);
        $nbCom = $commentRepository->countTrikComments($trick);

        if($nbComments >= $nbCom)
        {
            $nbComments = 0;
        }
        else
        {
            $nbComments++;
        }
        return $this->render('trick/show.html.twig', [
            'trick'         =>  $trick,
            'comments'      =>  $comments,
            'current-menu'  =>  'tricks',
            'form1'          =>  $form1->createView(),
            'form2'          =>  $form2->createView(),
            'nbComments'    =>  $nbComments
            ]);
    }
}