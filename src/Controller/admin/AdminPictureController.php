<?php
namespace App\Controller\admin;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPictureController extends AbstractController
{
    /**
     * @Route("admin/picture/edit/{id}/{idPicture}", name="admin.picture.edit")
     */
    public function edit(Trick $trick, int $idPicture, Request $request)
    {
        
        return $this->render('admin/picture/edit.html.twig');
    }

    /**
     * @Route("/admin/picture/{id}/{idPicture}", name="admin.picture.delete", methods="DELETE")
     * @param Trick $trick
     * @param int $idPicture
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Trick $trick, int $idPicture, Request $request)
    {
        if($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token')))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $pictures = $trick->getPictures();
            foreach($pictures as $picture)
            { 
                if($picture->getId() == $idPicture)
                {
                    $trick->removePicture($picture);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute("admin.trick.edit", [
                'id' =>  $trick->getId(),
            ]);
        }
        return $this->redirectToRoute('pages/home.html.twig');
    }
}