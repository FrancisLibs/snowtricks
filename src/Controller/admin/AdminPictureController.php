<?php
namespace App\Controller\admin;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\PictureUploadType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPictureController extends AbstractController
{
    /**
     * @Route("/admin/upload/picture/{id}", name="admin.trick.upload.picture", methods={"post"})
     * @param Request $request
     * @return JsonResponse|FormInterface
     */
    public function uploadFile(Request $request)
    {
        $form = $this->createForm(PictureUploadType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            return new JsonResponse([], 201);
        }

        return $form;
    }

    /**
     * @Route("/admin/picture/edit/{id}", name="admin.picture.edit", methods={"POST"})
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse|FormInterface
     */
    public function uploadAction(Picture $picture, Request $request, EntityManagerInterface $manager)
    {
        $trick = $picture->getTrick();

        // On efface l'ancienne image
        $fileName = $picture->getFilename();
        $trick->removePicture($picture);

        // Suppression du fichier
        unlink($this->getParameter('pictures_directory').'/'.$fileName);

        // On récupère la nouvelle image
        $file = $request->files->get('file');
        
        // Traitement du nom du nouveau fichier
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->getParameter('pictures_directory'), $newFilename);

        $picture = new Picture();
        $picture->setTrick($trick);
        $picture->setFilename($newFilename);

        $trick->addPicture($picture);

        $manager->persist($trick);
        $manager->flush();

        return $this->render('admin/trick/picture.html.twig',[
            'picture'  => $picture,
        ]);
    }

    /**
     * @Route("/admin/suppressPicture/{id}", name="admin.picture.delete", methods={"DELETE"})
     *
     * @param Picture $picture
     * @param Request $request
     */
    public function deletePicture(Picture $picture, Request $request, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);
       
        // Vérification du token
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {

            $manager->remove($picture);
            $manager->flush();

            //Retour d'un tableau json
            return new JsonResponse(['success' => 1]);
        } 
            return new JsonResponse(['error' => 'Token invalid'], 400);
    }
       
}