<?php

namespace App\Controller\admin;

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
        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/picture/delete", name="delete.picture")
     */
    public function delete(?UserInterface $user)
    {
        $user = $this->getUser();

        $user->setUserPicture('');
        
        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/picture/replace", name="change.picture")
     */
    public function changeUserPicture(?UserInterface $user)
    {
        $user = $this->getUser();

        $user->setUserPicture('');

        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }

}
