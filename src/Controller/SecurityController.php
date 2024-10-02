<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private LoggerInterface $logger;
    private TokenStorageInterface $tokenStorage;

    public function __construct(LoggerInterface $logger, TokenStorageInterface $tokenStorage)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->logger->info('Failed to log in', ['error' => $error]);
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('app_books');
        }

        return $this->render('security/login.html.twig', [
            'error' => $error,
        ]);
    }

    private function loginUser(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }

    #[Route(path: '/signup', name: 'app_signup')]
    public function signup(
        Request $request, 
        UserPasswordHasherInterface $passwordEncoder, 
        EntityManagerInterface $entityManager,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->logger->info('Failed to sign up', ['error' => $error]);
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('app_books');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
    
            $entityManager->persist($user);
            $entityManager->flush();

            $this->loginUser($user);
    
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signup.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
