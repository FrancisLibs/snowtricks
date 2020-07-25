<?php
namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\VideoRepository;
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
     * @Route("/admin/video/edit/{id}/{videoId}", name="admin.video.edit", methods={"POST"})
     *
     * @param Request $request
     * @param Trick $trick
     * @param int $pictureId
     * @return JsonResponse|FormInterface
     */
    public function uploadAction(Trick $trick, int $videoId, Request $request, EntityManagerInterface $manager,
        VideoRepository $repository) 
    {
        if ($request->isXmlHttpRequest()) 
        {
            // On efface l'ancienne image
            $video = $repository->findOneById($videoId);
            $trick->removeVideo($video);

            $link = $request->request->get('link');

            $video = new Video();
            $video->setLink($link);

            $trick->addVideo($video);

            $manager->persist($video);
            $manager->flush();

            return $this->json(['success' => 1]);
        }
    }
}