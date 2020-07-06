<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    const ENREGISTRE = 1;
    const VALIDE = 2;

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, 
    UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, UrlGeneratorInterface $generator)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $user->setStatus(self::ENREGISTRE);
            $token = uniqid();
            $user->setToken(random_bytes(20));

            $manager->persist($user);
            $manager->flush();

            $url = $generator->generate(
                'security_registration_validation', [
                    'token' => $token,
                    'id'    => $user->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $this->notify($url, $mailer, $user );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form'  =>  $form->createView()
        ]);
    }

    /**
     * @Route("/validation/{id}/{token}", name="security_registration_validation")
     */
    public function inscritpionValidation(User $user, Request $request, string $token, EntityManagerInterface $manager)
    {
        $token = $request->query->get('token');
        if($token == $user->getToken())
        {
            $user->setStatus(2);
           // $role = {'ROLE_ADMIN'};
            $user->setRoles($role);

            $manager->flush();
        }

        return $this->redirectToRoute('home');
    } 

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('homepage');
    }

    public function notify($url, $mailer, $user)
    {
        $message = (new \Swift_Message('Activation de votre compte'))
            ->setFrom('fr.libs@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig', [
                        'url'   => $url,
                    ]
                ),
                'text/plain'
            )
        ;
        $mailer->send($message);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }
}
