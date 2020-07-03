<?php

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\TrickType;
use Cocur\Slugify\Slugify;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
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
    public function new(Request $request, UserInterface $user) {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($user);
            
            $this->manager->persist($trick);
            $this->manager->flush();

            return $this->redirectToRoute('trick.show', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
            ]);
        }

        return $this->render('admin/trick/new.html.twig', [
            'trick'     =>  $trick,
            'form'      =>  $form->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/edit/{id}", name="admin.trick.edit", methods="GET|POST")
     * @param Trick $trick
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function edit(Trick $trick, Request $request): Response
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                $this->addFlash('success', 'trick modifié');

                return $this->redirectToRoute('admin.trick.edit', [
                    'id' =>  $trick->getId(),
                ]);
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' =>  $trick,
            'form'  =>  $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/trick/delete/{id}", name="admin.trick.delete", methods="DELETE")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($trick);
            $manager->flush();

            return $this->redirectToRoute('tricks.index');
        }

        return $this->render('pages/home.html.twig');
    }

    /**
     * @Route("/admin/mainPicture/delete/{id}", name="admin.mainPicture.delete")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureDelete(Trick $trick, EntityManagerInterface $manager, UploaderHelper $helper): Response
    {
        $picture = $trick->getMainPicture();
        if ($picture) {
            $fileName = $picture->getfileName();
            unlink('media/tricks/' . $fileName);
            $trick->setMainPicture(null);
            $manager->remove($picture);

            $manager->flush();
        }

        return $this->redirectToRoute('trick.show', [
            'slug'  =>  $trick->getSlug(),
            'id'    =>  $trick->getId(),
        ]);
    }
}
