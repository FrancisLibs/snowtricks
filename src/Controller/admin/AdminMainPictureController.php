<?php 

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\UploadType;
use App\Form\PictureType;
use Cocur\Slugify\Slugify;
use App\Form\TrickPictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMainPictureController extends AbstractController
{
    /**
     * @Route("/admin/mainPicture/edit/{id}", name="admin.mainPicture.edit")
     * @param Trick $trick
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureEdit(Trick $trick, Request $request, EntityManagerInterface $manager, 
    PictureRepository $repository): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $pictureFile = $form->get('file')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename =(new Slugify())->slugify($originalFilename);
                $newFilename = '/build/'.$safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                $pictureFile->move($this->getParameter('pictures_directory'), $newFilename);

                $trick->setMainPicture($newFilename);
                
                $manager->flush();

                return $this->redirectToRoute('trick.show', [
                    'id'    =>  $trick->getId(),
                    'slug'  =>  $trick->getSlug()
                ]);
            }
        }

        return $this->render('admin/mainPicture/edit.html.twig', [
            'trick' =>  $trick,
            'id'    =>  $trick->getId(),
            'form'  =>  $form->createView()
        ]);
    }

    /**
     * @Route("/admin/mainPicture/delete/{id}", name="admin.mainPicture.delete")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureDelete(Trick $trick, EntityManagerInterface $manager): Response
    {
        $mainPicture = $trick->getMainPicture();
        if(isset($mainPicture))
        {
            $trick->setMainPicture('build/empty.jpg');
        }

        $manager->flush();

        return $this->redirectToRoute('trick.show', [
            'slug'  =>  $trick->getSlug(),
            'id'    =>  $trick->getId(),
        ]);
    }
    
    /**
     * @Route("/admin/mainPicture/choice/{id}/{pictureId}", name="admin.mainPicture.choice")
     * @param int $pictureId
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureChoice(Trick $trick, int $pictureId, EntityManagerInterface $manager): Response
    {
        $pictures = $trick->getPictures();

        foreach($pictures as $picture)
        {
            if($picture->getId() == $pictureId)
            {
                $picture->setMainPicture(TRUE);
            }
            else
            {
                $picture->setMainPicture(FALSE);
            }
        }

        $manager->flush();
        
        return $this->redirectToRoute('trick.show', ['slug'=>$trick->getSlug(), 'id'=>$trick->getId()]);
    }
}