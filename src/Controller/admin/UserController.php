<?php

namespace App\Controller\admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function profil(?UserInterface $user)
    {
        $user = $this->getUser();

        return $this->render('user/profil.html.twig', [
            'user'  =>  $user,
        ]);
    }


    /**
     * @Route("/picture/replace/{id}", name="change.picture")
     */
    public function changeUserPicture(User $user,Request $request, EntityManagerInterface $manager )
    {
        $file = $request->files->get('file');
        
        if($file)
        {
            // Effacement de l'ancienne image
            $this->deleteUserPicture($user);

            // Traitement du nom du nouveau fichier
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            $file->move($this->getParameter('user_pictures'), $newFilename);

            $user->setUserPicture($newFilename);
            $manager->flush();

            return $this->render('user/newUserPicture.html.twig', [
                'user'  =>  $user,
            ]);
        }

        return $this->render('user/newUserPicture.html.twig', [
            'user'  =>  $user,
        ]);
    }

    public function deleteUserPicture($user) // fonction d'effacement d'image
    {
        $user = $this->getUser();
        $picture = $user->getUserPicture();

        if($picture)
        {
            unlink($this->getParameter('user_pictures'). $picture);
        }

        return new Response('true');
    }

}
