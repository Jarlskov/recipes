<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SessionTimeoutSubscriber implements EventSubscriberInterface
{
    private const SESSION_TIMEOUT = 3600; // 1 hour in seconds
    private const LAST_ACTIVITY_KEY = '_security.last_activity';

    public function __construct(
        private RequestStack $requestStack,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 8], // Run before security
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        
        // Skip for public routes
        $publicRoutes = ['login', 'register', 'app_forgot_password_request', 'app_check_email', 'app_reset_password'];
        $routeName = $request->attributes->get('_route');
        
        if (in_array($routeName, $publicRoutes, true)) {
            return;
        }

        // Check if user is authenticated
        if (!$this->tokenStorage->getToken() || !$this->tokenStorage->getToken()->isAuthenticated()) {
            return;
        }

        $currentTime = time();
        $lastActivity = $session->get(self::LAST_ACTIVITY_KEY);

        // If no last activity time is set, set it now
        if (!$lastActivity) {
            $session->set(self::LAST_ACTIVITY_KEY, $currentTime);
            return;
        }

        // Check if session has expired
        if (($currentTime - $lastActivity) > self::SESSION_TIMEOUT) {
            // Session has expired, invalidate it
            $session->invalidate();
            $this->tokenStorage->setToken(null);
            
            // Add flash message for user feedback
            $session->getFlashBag()->add('warning', 'Your session has expired due to inactivity. Please log in again.');
            
            // Redirect to login page
            $event->setResponse(new \Symfony\Component\HttpFoundation\RedirectResponse('/login'));
            return;
        }

        // Update last activity time
        $session->set(self::LAST_ACTIVITY_KEY, $currentTime);
    }
}

