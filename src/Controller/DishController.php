<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Dish;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dish', name: 'dish_')]
class DishController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $dishes = $this->entityManager->getRepository(Dish::class)->findAll();

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $dish = new Dish();
        
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $dailyMealFriendly = $request->request->getBoolean('daily_meal_friendly');
            $prepFriendly = $request->request->getBoolean('prep_friendly');
            $freezerFriendly = $request->request->getBoolean('freezer_friendly');

            if ($name && $description) {
                $dish->setName($name);
                $dish->setDescription($description);
                $dish->setDailyMealFriendly($dailyMealFriendly);
                $dish->setPrepFriendly($prepFriendly);
                $dish->setFreezerFriendly($freezerFriendly);

                $this->entityManager->persist($dish);
                $this->entityManager->flush();

                $this->addFlash('success', 'Dish created successfully!');
                return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
            }

            $this->addFlash('error', 'Name and description are required.');
        }

        return $this->render('dish/new.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Dish $dish): Response
    {
        return $this->render('dish/show.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Dish $dish): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $dailyMealFriendly = $request->request->getBoolean('daily_meal_friendly');
            $prepFriendly = $request->request->getBoolean('prep_friendly');
            $freezerFriendly = $request->request->getBoolean('freezer_friendly');

            if ($name && $description) {
                $dish->setName($name);
                $dish->setDescription($description);
                $dish->setDailyMealFriendly($dailyMealFriendly);
                $dish->setPrepFriendly($prepFriendly);
                $dish->setFreezerFriendly($freezerFriendly);

                $this->entityManager->flush();

                $this->addFlash('success', 'Dish updated successfully!');
                return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
            }

            $this->addFlash('error', 'Name and description are required.');
        }

        return $this->render('dish/edit.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Dish $dish): Response
    {
        $this->entityManager->remove($dish);
        $this->entityManager->flush();

        $this->addFlash('success', 'Dish deleted successfully!');
        return $this->redirectToRoute('dish_index');
    }
}
