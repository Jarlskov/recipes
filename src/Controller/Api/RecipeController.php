<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Entity\FullRecipeVersion;
use App\Entity\LinkRecipeVersion;
use App\Entity\BookRecipeVersion;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RecipeRepository $recipeRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'api_recipes_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): JsonResponse
    {
        $recipes = $this->recipeRepository->findByOwner($this->getUser());

        $data = [];
        foreach ($recipes as $recipe) {
            $data[] = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'createdAt' => $recipe->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $recipe->getUpdatedAt()->format('Y-m-d H:i:s'),
                'versionsCount' => $recipe->getVersions()->count(),
                'latestVersion' => $recipe->getLatestVersion() ? [
                    'id' => $recipe->getLatestVersion()->getId(),
                    'name' => $recipe->getLatestVersion()->getName(),
                    'type' => $recipe->getLatestVersion() instanceof FullRecipeVersion ? 'full' : 
                             ($recipe->getLatestVersion() instanceof LinkRecipeVersion ? 'link' : 'book')
                ] : null
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'api_recipes_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $recipe = new Recipe();
        $recipe->setName($data['name'] ?? '');
        $recipe->setOwner($this->getUser());

        // Validate the recipe
        $errors = $this->validator->validate($recipe);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Recipe created successfully',
            'recipe' => [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'createdAt' => $recipe->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_recipes_show', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Recipe $recipe): JsonResponse
    {
        // Check if user owns this recipe
        if ($recipe->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $versions = [];
        foreach ($recipe->getVersions() as $version) {
            $versionData = [
                'id' => $version->getId(),
                'name' => $version->getName(),
                'recipeName' => $version->getRecipeName(),
                'createdAt' => $version->getCreatedAt()->format('Y-m-d H:i:s'),
                'type' => $version instanceof FullRecipeVersion ? 'full' : 
                         ($version instanceof LinkRecipeVersion ? 'link' : 'book')
            ];

            if ($version instanceof FullRecipeVersion) {
                $versionData['description'] = $version->getDescription();
                $versionData['ingredients'] = [];
                foreach ($version->getIngredients() as $ingredient) {
                    $versionData['ingredients'][] = [
                        'id' => $ingredient->getId(),
                        'name' => $ingredient->getName(),
                        'amount' => $ingredient->getAmount(),
                        'unit' => $ingredient->getUnit(),
                        'orderNumber' => $ingredient->getOrderNumber()
                    ];
                }
                $versionData['steps'] = [];
                foreach ($version->getSteps() as $step) {
                    $versionData['steps'][] = [
                        'id' => $step->getId(),
                        'description' => $step->getDescription(),
                        'orderNumber' => $step->getOrderNumber()
                    ];
                }
            } elseif ($version instanceof LinkRecipeVersion) {
                $versionData['url'] = $version->getUrl();
                $versionData['author'] = $version->getAuthor();
            } elseif ($version instanceof BookRecipeVersion) {
                $versionData['bookTitle'] = $version->getBookTitle();
                $versionData['author'] = $version->getAuthor();
                $versionData['pageNumber'] = $version->getPageNumber();
            }

            $versions[] = $versionData;
        }

        return $this->json([
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'createdAt' => $recipe->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $recipe->getUpdatedAt()->format('Y-m-d H:i:s'),
            'versions' => $versions
        ]);
    }

    #[Route('/{id}', name: 'api_recipes_update', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function update(Request $request, Recipe $recipe): JsonResponse
    {
        // Check if user owns this recipe
        if ($recipe->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $recipe->setName($data['name']);
        }

        // Validate the recipe
        $errors = $this->validator->validate($recipe);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Recipe updated successfully',
            'recipe' => [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'updatedAt' => $recipe->getUpdatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_recipes_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Recipe $recipe): JsonResponse
    {
        // Check if user owns this recipe
        if ($recipe->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Recipe deleted successfully'
        ]);
    }
} 