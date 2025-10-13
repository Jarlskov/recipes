<?php

declare(strict_types=1);

namespace App\Controller\Dish;

use App\Entity\Dish;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dish/{id}/recipe', name: 'dish_recipe_', requirements: ['id' => '\d+'])]
class RecipeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, Dish $dish): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipe = new Recipe();
        $recipe->setDish($dish);
        
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                // Process ingredients
                foreach ($recipe->getIngredients() as $ingredient) {
                    $ingredient->setRecipe($recipe);
                    $this->entityManager->persist($ingredient);
                }
                
                $dish->addRecipe($recipe);
                $this->entityManager->persist($recipe);
                $this->entityManager->flush();

                $this->addFlash('success', 'Recipe created successfully!');
                return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
            }

        return $this->render('dish/recipe/new.html.twig', [
            'form' => $form,
            'dish' => $dish,
        ]);
    }

    #[Route('/{recipeId}', name: 'show', methods: ['GET'], requirements: ['recipeId' => '\d+'])]
    public function show(Dish $dish, int $recipeId): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipe = $this->entityManager->getRepository(Recipe::class)->find($recipeId);
        
        if (!$recipe || $recipe->getDish() !== $dish) {
            throw $this->createNotFoundException('Recipe not found');
        }

        return $this->render('dish/recipe/show.html.twig', [
            'recipe' => $recipe,
            'dish' => $dish,
        ]);
    }

    #[Route('/{recipeId}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['recipeId' => '\d+'])]
    public function edit(Request $request, Dish $dish, int $recipeId): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipe = $this->entityManager->getRepository(Recipe::class)->find($recipeId);
        
        if (!$recipe || $recipe->getDish() !== $dish) {
            throw $this->createNotFoundException('Recipe not found');
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Process ingredients
            foreach ($recipe->getIngredients() as $ingredient) {
                $ingredient->setRecipe($recipe);
                $this->entityManager->persist($ingredient);
            }
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Recipe updated successfully!');
            return $this->redirectToRoute('dish_recipe_show', ['id' => $dish->getId(), 'recipeId' => $recipe->getId()]);
        }

        return $this->render('dish/recipe/edit.html.twig', [
            'form' => $form,
            'dish' => $dish,
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{recipeId}/delete', name: 'delete', methods: ['POST'], requirements: ['recipeId' => '\d+'])]
    public function delete(Dish $dish, int $recipeId): Response
    {
        $this->denyAccessUnlessGranted('OWNER', $dish);
        
        $recipe = $this->entityManager->getRepository(Recipe::class)->find($recipeId);
        
        if (!$recipe || $recipe->getDish() !== $dish) {
            throw $this->createNotFoundException('Recipe not found');
        }

        $dish->removeRecipe($recipe);
        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        $this->addFlash('success', 'Recipe deleted successfully!');
        return $this->redirectToRoute('dish_show', ['id' => $dish->getId()]);
    }
}
