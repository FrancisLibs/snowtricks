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
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/admin/trick/new", name="admin.trick.create")
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $pictures = $form->get('pictures')->getData();

            foreach($pictures as $picture)
            {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = (new Slugify())->slugify($originalFilename);
                $newFilename = '/build/' . $safeFilename . '-' . uniqid() . '.' . $picture->guessExtension();

                $picture->move($this->getParameter('pictures_directory'), $newFilename);

                $newPicture = new Picture();
                $newPicture->setFile($newFilename);
                $trick->addPicture($newPicture);
            }

            $user = $this->getUser();
            $trick->setUser($user);
            $trick->setMainPicture('build/empty.jpg');
            $manager->persist($trick);
            $manager->flush();

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
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // Récupération des images transmises
            $pictures = $form->get('pictures')->getData();

            // Boucle de traitement
            foreach ($pictures as $picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = (new Slugify())->slugify($originalFilename);
                $newFilename = '/build/' . $safeFilename . '-' . uniqid() . '.' . $picture->guessExtension();

                $picture->move($this->getParameter('pictures_directory'), $newFilename);

                $newPicture = new Picture();
                $newPicture->setFile($newFilename);
                $trick->addPicture($newPicture);
            }

            $manager->flush();

            return $this->redirectToRoute('trick.show', [
                'slug'  =>  $trick->getSlug(),
                'id'    =>  $trick->getId(),
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
     * @Route("admin/suppressPicture/{id}", name="admin.picture.delete", methods={"DELETE"})
     *
     * @param Picture $picture
     * @param Request $request
     */
    public function deletePicture(Picture $picture, Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        // Vérification du token
        if($this->isCsrfTokenValid('delete'.$picture->getId(), $data['_token']))
        {
            $nom = $picture->getFile();
            // Suppression du fichier
            unlink($this->getParameter('pictures_directory').'/'.$nom);

            $manager->remove($picture);
            $manager->flush();

             //Retour d'un tabelau json
            return new JsonResponse(['success' => 1]);
        }
        else
        {
            return new JsonResponse(['error'=> 'Token invalid'], 400);
        }
    }
}
