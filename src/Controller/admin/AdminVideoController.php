<?php
namespace App\Controller\admin;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminVideoController extends AbstractController
{
    /**
     * @Route("/admin/suppressVideo/{id}", name="admin.video.delete", methods={"DELETE"})
     *
     * @param Video $video
     * @param Request $request
     */
    public function deletePicture(Video $video, Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        // Vérification du token
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $data['_token'])) {
            $manager->remove($video);
            $manager->flush();

            //Retour d'un tabelau json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalid'], 400);
        }
    }
}