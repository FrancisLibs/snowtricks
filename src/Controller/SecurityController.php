<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, 
    UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $token = uniqid();
            $user->setToken(random_bytes(20));

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form'  =>  $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="app_login", methods={"GET", "POST"})
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

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/forgotten_password", name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $manager): Response 
    {

        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
            
            /* @var $user User */
            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('tricks.index');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('tricks.index');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Mot de passe oublié !'))
            ->setFrom('fr.libs@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    [
                        'url'   => $url,
                    ]
                ),
                'text/plain'
            );
            $mailer->send($message);

            $this->addFlash('notice', 'Mail envoyé');

            return $this->redirectToRoute('tricks.index');
        }

        return $this->render('security/forgotten_password.html.twig');
    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager )
    {

        if ($request->isMethod('POST')) {

            $user = $manager->getRepository(User::class)->findOneBy(['resetToken' =>  $token]);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('tricks.index');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $manager->flush();

            $this->addFlash('notice', 'Mot de passe mis à jour');

            return $this->redirectToRoute('tricks.index');
        } else {

            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }

    /**
     * Affiche et traite le formulaire de reset du password
     * @Route("/user/resetPassword", name="user.password.reset")
     *
     * @return response
     */
    public function passwordReset(UserInterface $user, Request $request, UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $manager, UserRepository $repository)
    {
        $user = $this->getUser();

        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get("oldPassword")->getData();

            if ($encoder->isPasswordValid($user, $oldPassword)) {
                $hash = $encoder->encodePassword($user, $user->getPlainPassword());

                $user->setPassword($hash);
                $user->setPlainPassword("");

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'le changement de mot de passe a bien été pris en compte');

                return $this->redirectToRoute('home');
            } else {
                $this->addFlash('danger', 'l\'ancien mot de passe est erroné');
            }
        }

        return $this->render('user/passwordReset.html.twig', [
            'form'  =>  $form->createView()
        ]);
    }
}
