<?php
namespace App\Controller\admin;

use App\Entity\Trick;
use App\Form\PictureUploadType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPictureController extends AbstractController
{
    /**
     * @Route("admin/picture/edit/{id}/{idPicture}", name="admin.picture.edit")
     */
    public function edit(int $idPicture, Request $request)
    {
        return $this->render('admin/picture/edit.html.twig');
    }

    /**
     * @Route("/admin/picture/{id}/{idPicture}", name="admin.picture.delete", methods="DELETE")
     * @param Trick $trick
     * @param int $idPicture
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Trick $trick, int $idPicture, Request $request, EntityManagerInterface $manager)
    {
        if($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token')))
        {
            $pictures = $trick->getPictures();
            foreach($pictures as $picture)
            { 
                if($picture->getId() == $idPicture)
                {
                    $trick->removePicture($picture);
                }
            }
            $manager->flush();

            return $this->redirectToRoute("admin.trick.edit", [
                'id' =>  $trick->getId(),
            ]);
        }
        return $this->redirectToRoute('pages/home.html.twig');
    }

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
            $this->getDoctrine()
                ->getRepository('AppBundle:File')
                ->store($form->getData());

            return new JsonResponse([], 201);
        }

        return $form;
    }

    /**
     * @Route("/admin/upload/picture/", name="upload.picture", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse|FormInterface
     */
    public function uploadAction(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
        $form = $this->createForm(PictureUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->getData();
            //$this->getDoctrine()
             //   ->getRepository('AppBundle:File')
             //   ->store($file);

            return new JsonResponse(["key" => $file->getDocumentKey()], 201);
        }

        return $form;
    }
}