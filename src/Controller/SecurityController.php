<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private RateLimiterFactory $loginAttemptsLimiter,
        private RateLimiterFactory $loginAttemptsPerIpLimiter
    ) {
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session): Response
    {
        // Check if user is already authenticated
        if ($this->getUser()) {
            return $this->redirectToRoute('dish_index');
        }

        // Rate limiting by IP address
        $clientIp = $request->getClientIp();
        $ipLimiter = $this->loginAttemptsPerIpLimiter->create($clientIp);
        
        if (!$ipLimiter->consume()->isAccepted()) {
            $this->addFlash('error', 'Too many login attempts from this IP address. Please try again later.');
            return $this->render('security/login.html.twig', [
                'last_username' => '',
                'error' => null,
            ]);
        }

        // Rate limiting by username (if provided)
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($lastUsername) {
            $userLimiter = $this->loginAttemptsLimiter->create($lastUsername);
            
            if (!$userLimiter->consume()->isAccepted()) {
                $this->addFlash('error', 'Too many login attempts for this account. Please try again later.');
                return $this->render('security/login.html.twig', [
                    'last_username' => $lastUsername,
                    'error' => null,
                ]);
            }
        }

        // Regenerate session ID to prevent session fixation attacks
        if (!$session->isStarted()) {
            $session->start();
        }
        $session->migrate();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if user already exists
            $existingUser = $this->entityManager->getRepository(User::class)->findByEmail($user->getEmail());
            if ($existingUser) {
                $this->addFlash('error', 'An account with this email already exists.');
                return $this->render('security/register.html.twig', [
                    'form' => $form,
                ]);
            }

            // Hash the password
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Account created successfully! You can now log in.');
            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
