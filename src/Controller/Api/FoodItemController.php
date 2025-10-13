<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\FoodItem;
use App\Repository\FoodItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/food-items', name: 'api_food_items_')]
class FoodItemController extends AbstractController
{
    public function __construct(
        private FoodItemRepository $foodItemRepository
    ) {
    }

    #[Route('/autocomplete', name: 'autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        if (strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $foodItems = $this->foodItemRepository->findByNameContaining($query, 10);
        
        $results = array_map(function (FoodItem $foodItem) {
            return [
                'id' => $foodItem->getId(),
                'name' => $foodItem->getName(),
                'description' => $foodItem->getDescription(),
            ];
        }, $foodItems);

        return new JsonResponse($results);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        if (empty($name)) {
            return new JsonResponse(['error' => 'Name is required'], 400);
        }

        // Check if food item already exists
        $existingFoodItem = $this->foodItemRepository->findOneBy(['name' => $name]);
        if ($existingFoodItem) {
            return new JsonResponse([
                'id' => $existingFoodItem->getId(),
                'name' => $existingFoodItem->getName(),
                'description' => $existingFoodItem->getDescription(),
            ]);
        }

        // Create new food item
        $foodItem = new FoodItem();
        $foodItem->setName($name);
        $foodItem->setDescription($description ?: 'No description provided');

        $this->foodItemRepository->save($foodItem, true);

        return new JsonResponse([
            'id' => $foodItem->getId(),
            'name' => $foodItem->getName(),
            'description' => $foodItem->getDescription(),
        ]);
    }
}
