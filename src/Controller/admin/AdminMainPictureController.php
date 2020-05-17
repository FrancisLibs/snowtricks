<?php 

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMainPictureController extends AbstractController
{
     /**
     * @Route("/admin/mainPicture/delete/{id}", name="admin.mainPicture.delete")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureDelete(Trick $trick): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $pictures = $trick->getPictures();
        foreach($pictures as $picture)
        {
            $picture->setMainPicture(FALSE);
        }
        $entityManager->flush();

        return $this->redirectToRoute('trick.show', [
            'slug'  =>  $trick->getSlug(),
            'id'    =>  $trick->getId()
        ]);
    }
    /**
     * @Route("/admin/mainPicture/edit/{id}", name="admin.mainPicture.edit")
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPictureEdit(Trick $trick): Response
    {
        return $this->render('admin/mainPicture/edit.html.twig', [
            'trick' =>  $trick,
            'id'    =>  $trick->getId()
            ]);
    }
    /**
     * @Route("/admin/mainPicture/choice/{id}/{pictureId}", name="admin.mainPicture.choice")
     */
    public function mainPictureChoice(Trick $trick, int $pictureId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

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

        $entityManager->flush();
        
        return $this->redirectToRoute('trick.show', ['slug'=>$trick->getSlug(), 'id'=>$trick->getId()]);
    }
}