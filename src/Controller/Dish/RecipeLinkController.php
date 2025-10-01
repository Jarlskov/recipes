<?php

declare(strict_types=1);

namespace App\Controller\Dish;

use App\Entity\Dish;
use App\Entity\RecipeLink;
use App\Form\RecipeLinkType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dish/{dishId}/recipe-link', name: 'dish_recipe_link_', requirements: ['dishId' => '\d+'])]
class RecipeLinkController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipeLink = new RecipeLink($dish);
        $form = $this->createForm(RecipeLinkType::class, $recipeLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dish->addRecipeLink($recipeLink);
            $this->entityManager->persist($recipeLink);
            $this->entityManager->flush();

            $this->addFlash('success', 'Recipe link added successfully!');
            return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
        }

        return $this->render('dish/recipe_link/new.html.twig', [
            'form' => $form,
            'dish' => $dish,
        ]);
    }

    #[Route('/{linkId}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['linkId' => '\d+'])]
    public function edit(Request $request, Dish $dish, int $linkId): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipeLink = $this->entityManager->getRepository(RecipeLink::class)->find($linkId);
        
        if (!$recipeLink || $recipeLink->getDish() !== $dish) {
            throw $this->createNotFoundException('Recipe link not found');
        }

        $form = $this->createForm(RecipeLinkType::class, $recipeLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Recipe link updated successfully!');
            return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
        }

        return $this->render('dish/recipe_link/edit.html.twig', [
            'form' => $form,
            'dish' => $dish,
            'recipeLink' => $recipeLink,
        ]);
    }

    #[Route('/{linkId}/delete', name: 'delete', methods: ['POST'], requirements: ['linkId' => '\d+'])]
    public function delete(Dish $dish, int $linkId): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipeLink = $this->entityManager->getRepository(RecipeLink::class)->find($linkId);
        
        if (!$recipeLink || $recipeLink->getDish() !== $dish) {
            throw $this->createNotFoundException('Recipe link not found');
        }

        $dish->removeRecipeLink($recipeLink);
        $this->entityManager->remove($recipeLink);
        $this->entityManager->flush();

        $this->addFlash('success', 'Recipe link deleted successfully!');
        return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
    }
}
