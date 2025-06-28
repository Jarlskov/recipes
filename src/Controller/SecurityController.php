<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            return $this->json([
                'error' => $error->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        // If we get here, authentication was successful
        return $this->json([
            'message' => 'Login successful',
            'user' => [
                'email' => $this->getUser()->getUserIdentifier(),
                'roles' => $this->getUser()->getRoles()
            ]
        ]);
    }

    #[Route(path: '/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
        return $this->json([
            'message' => 'Logout successful'
        ]);
    }
}
