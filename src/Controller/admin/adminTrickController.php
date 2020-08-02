<?php

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\MainPictureType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $repository;

    /**
     *
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(TrickRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/trick/new", name="admin.trick.create")
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function newTrick(Request $request, UserInterface $user) 
    {
        $trick = new Trick();
        $form2 = $this->createForm(TrickType::class, $trick);

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            $trick->setUser($user);
            $this->manager->persist($trick);
            $this->manager->flush();

            $this->addFlash("notice", "Le trick a été créé !");

            return $this->redirectToRoute('home');
        }

        return $this->render('admin/trick/new.html.twig', [
            'trick'     =>  $trick,
            'form2'     =>  $form2->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/edit/{id}", name="admin.trick.edit", methods="GET|POST")
     * @param Trick $trick
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editTrick(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        // Modification de l'image à la une
        $form1 = $this->createForm(MainPictureType::class, $trick);
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('admin.trick.edit', [
                'id' => $trick->getId(),
            ]);
        }

        $form2 = $this->createForm(TrickType::class, $trick);

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
                $this->manager->flush();
                $this->addFlash('success', 'trick modifié');

                $this->addFlash("notice", "Les modifications ont été enregistrées !");

                return $this->redirectToRoute('home');
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' =>  $trick,
            'form1' =>  $form1->createView(),
            'form2' =>  $form2->createView(),
        ]);
    }

    /**
     * @Route("/admin/trick/delete/{id}", name="admin.trick.delete", methods="DELETE")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteTrick(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'];

        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $token))
        {
            $manager->remove($trick);
            $manager->flush();

            return $this->json(['success' => 1]);
        }

        return $this->json(['success' => 0]);
    }

    /**
     * @Route("/admin/mainPicture/delete/{id}", name="admin.mainPicture.delete")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureDelete(Trick $trick, EntityManagerInterface $manager): Response
    {
        $pictureFile = $trick->getMainFileName();
        unlink('media/trickMain/' . $pictureFile);
        $trick->setMainFileName(null);

        $manager->flush();
        
        return $this->redirectToRoute('trick.show', [
            'slug'  =>  $trick->getSlug(),
            'id'    =>  $trick->getId(),
        ]);
    }
}
