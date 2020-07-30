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
    public function deleteVideo(Video $video, Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        // Vérification du token
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $data['_token'])) {
            $trick = $video->getTrick();
            $trick->removeVideo($video);
            $manager->remove($video);
            $manager->flush();

            //Retour d'un tabelau json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalid'], 400);
        }
    }

    /**
     * @Route("/admin/video/edit/{id}", name="admin.video.edit", methods={"POST"})
     *
     * @param Video $video
     * @param Request $request
     */
    public function uploadAction(Video $video, Request $request, EntityManagerInterface $manager) 
    {
        if ($request->isXmlHttpRequest()) 
        {
            $data = json_decode($request->getContent(), true);
            
            // On récupère le trick
            $trick = $video->getTrick();
            $trick->removeVideo($video);

            $link = $data['link'];

            $video = new Video();
            $video->setLink($link);

            $trick->addVideo($video);

            $manager->persist($video);
            $manager->persist($trick);
            $manager->flush();

            return $this->render('admin/trick/video.html.twig', [
                "video"  => $video,
            ]);
        }
    }
}