<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Redirect logged-in users directly to dishes
        if ($this->getUser()) {
            return $this->redirectToRoute('dish_index');
        }
        
        // Show welcome page for non-logged-in users
        return $this->render('home/index.html.twig');
    }
}
