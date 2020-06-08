<?php

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\TrickType;
use Cocur\Slugify\Slugify;
use App\Form\CreateTrickType;
use App\Form\PictureUploadType;
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
    public function new(Request $request, EntityManagerInterface $manager)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $trick->setUser($user);
            $manager->persist($trick);
            $manager->flush();

            if ($form->getClickedButton() === $form->get('save')) {
                return $this->redirectToRoute('trick.show', [
                    'slug'  =>  $trick->getSlug(),
                    'id'    =>  $trick->getId(),
                ]);
            }

            if ($form->getClickedButton() === $form->get('saveAndAdd')) {
                return $this->redirectToRoute('admin/trick/addMedia', [
                    'id'    => $trick->getId(),
                ]);
            }
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
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            if ($form->getClickedButton() === $form->get('save')) {
                return $this->redirectToRoute('trick.show', [
                    'slug'  =>  $trick->getSlug(),
                    'id'    =>  $trick->getId(),
                ]);
            }

            if ($form->getClickedButton() === $form->get('saveAndAdd')) {
                return $this->redirectToRoute('admin.trick.addMedia', [
                    'id' => $trick->getId(),
                ]);
            }
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
     * @Route("/admin/trick/addMedia/{id}", name="admin.trick.addMedia")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addmedia(Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        $picture = new Picture();
        $form = $this->createForm(PictureUploadType::class, $picture);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move($this->getParameter('pictures_directory'), $filename);

            $picture = new Picture();
            $picture
                ->setFile($filename)
                ->setTrick($trick);

            $trick->addPicture($picture);

            $manager->flush();

            return $this->render('admin/picture/picture.html.twig', [
                'image' =>  $filename,
            ]);
        }


        return $this->render('admin/trick/addMedia.html.twig', [
            'trick' =>  $trick,
            'form'  =>  $form->createView(),
        ]);
    }
}
