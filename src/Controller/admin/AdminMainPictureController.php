<?php 

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\PictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMainPictureController extends AbstractController
{
    /**
     * @Route("/admin/mainPicture/edit/{id}", name="admin.mainPicture.edit")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureEdit(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $pictureFileName = $form->get('picture')->getData();
            
            if($pictureFileName)
            {
                $originalFilename = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFileName->guessExtension();
                

                $pictureFileName->move($this->getParameter('pictures_directory'), $newFilename);

                $picture->setFileName($pictureFileName);

                $manager->persist($picture);
                $manager->flush();

                return $this->redirectToRoute('admin.mainPicture.edit',[
                    'trick' =>  $trick,
                    'id'    =>  $trick->getId()
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