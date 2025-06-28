<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setPassword($userPasswordHasher->hashPassword($user, $data['password'] ?? ''));

        // Validate the user
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        // Send confirmation email (simplified without Twig template)
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new Email())
                ->from(new Address('mailer@example.com', 'Recipe'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->text('Please confirm your email by clicking the link in your email.')
        );

        return $this->json([
            'message' => 'User registered successfully. Please check your email to verify your account.',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/verify/email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyUserEmail(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json([
                'error' => $exception->getReason()
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'message' => 'Your email address has been verified successfully.'
        ]);
    }
}
