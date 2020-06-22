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
     * @Route("/admin/mainPicture/delete/{id}", name="admin.mainPicture.delete")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureDelete(Trick $trick, EntityManagerInterface $manager): Response
    {
        $nom = $trick->getMainPicture();
        if (!empty($nom) && $nom != 'empty.jpg') 
        {
            unlink($this->getParameter('pictures_directory') . '/' . $nom);
            $trick->setMainPicture('empty.jpg');

            $manager->flush();
        }

        return $this->redirectToRoute('trick.show', [
            'slug'  =>  $trick->getSlug(),
            'id'    =>  $trick->getId(),
        ]);
    }
    
   
}