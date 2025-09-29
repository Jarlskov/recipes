<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
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
        $user = $this->getUser();
        $dishes = $this->entityManager->getRepository(Dish::class)->findBy(['owner' => $user]);

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $dish = new Dish();
        $dish->setOwner($this->getUser());
        
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($dish);
            $this->entityManager->flush();

            $this->addFlash('success', 'Dish created successfully!');
            return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
        }

        return $this->render('dish/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        return $this->render('dish/show.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Dish updated successfully!');
            return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
        }

        return $this->render('dish/edit.html.twig', [
            'form' => $form,
            'dish' => $dish,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $this->entityManager->remove($dish);
        $this->entityManager->flush();

        $this->addFlash('success', 'Dish deleted successfully!');
        return $this->redirectToRoute('dish_index');
    }
}
