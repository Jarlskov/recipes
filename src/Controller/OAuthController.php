<?php

declare(strict_types=1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OAuthController extends AbstractController
{
    #[Route('/connect/google', name: 'oauth_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): RedirectResponse
    {
        // will redirect to Google!
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile']);
    }

    #[Route('/connect/google/check', name: 'oauth_google_check')]
    public function connectGoogleCheck(Request $request, ClientRegistry $clientRegistry): Response
    {
        // **if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (see https://symfony.com/doc/current/security/guard_authenticators.html)

        return $this->redirectToRoute('dish_index');
    }
}
