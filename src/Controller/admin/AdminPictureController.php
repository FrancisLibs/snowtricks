<?php
namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Picture;
use Cocur\Slugify\Slugify;
use App\Form\PictureUploadType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AdminPictureController extends AbstractController
{
    /**
     * Route("/admin/upload/picture/{id}", name="admin.trick.upload.picture", methods={"post"})
     * @param Request $request
     * @return JsonResponse|FormInterface
     */
    public function uploadFile(Trick $trick, Request $request)
    {
        $form = $this->createForm(PictureUploadType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            /*$this->getDoctrine()
                ->getRepository('AppBundle:File')
                ->store($form->getData());*/

            return new JsonResponse([], 201);
        }

        return $form;
    }

    /**
     * @Route("/admin/picture/edit/{id}/{pictureId}", name="admin.picture.edit", methods={"POST"})
     *
     * @param Request $request
     * @param int $pictureId
     * @param Trick $trick
     * @return JsonResponse|FormInterface
     */
    public function uploadAction(Trick $trick, int $pictureId, Request $request, EntityManagerInterface $manager)
    {
        if($request->isXmlHttpRequest())// is it an Ajax request?
        {
            // On efface l'ancienne image
            //$picture = $PictureRepository->findOneById($pictureId);
            $fileName = $picture->getFile();
            $trick->removePicture($picture);

            // Suppression du fichier
            unlink($this->getParameter('pictures_directory').'/'.$fileName);

            // On récupère la nouvelle image
            $file = $request->files->get('file');/* Getting the file */
       
            // Traitement du nom du nouveau fichier
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // this is needed to safely include the file name as part of the URL
            $safeFilename = (new Slugify())->slugify($originalFileName);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            $file->move($this->getParameter('pictures_directory'), $newFilename);

            $picture = new Picture();
            $picture->setFile($newFilename);

            $trick->addPicture($picture);

            $manager->persist($picture);
            $manager->flush();

            $imagePath = $imagine->getUrlOfFilteredImage($this->getParameter('pictures_directory').'/'.$newFilename, 'my_thumb');
        }

        return $this->json(['newImage' => $imagePath]);
    }

    /**
     * @Route("/admin/suppressPicture/{id}", name="admin.picture.delete", methods={"DELETE"})
     *
     * @param Picture $picture
     * @param Request $request
     */
    public function deletePicture(Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        // Vérification du token
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
            $nom = $picture->getFile();
            
            // Suppression du fichier
            unlink($this->getParameter('pictures_directory') . '/' . $nom);

            $manager->remove($picture);
            $manager->flush();

            //Retour d'un tabelau json
            return $this->json(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalid'], 400);
        }
    }
}