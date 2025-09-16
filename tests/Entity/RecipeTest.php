<?php

namespace App\Tests\Entity;

use App\Entity\Dish;
use App\Entity\FoodItem;
use App\Entity\Ingredient;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\CookingSession;
use App\Service\Quantity;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    public function testRecipeBasicFields(): void
    {
        $recipe = new Recipe();
        
        // Test name
        $recipe->setName('Chocolate Cake');
        $this->assertEquals('Chocolate Cake', $recipe->getName());
        
        // Test description
        $description = 'A delicious chocolate cake recipe that is perfect for any occasion.';
        $recipe->setDescription($description);
        $this->assertEquals($description, $recipe->getDescription());
        
        // Test notes
        $notes = 'This recipe works best with high-quality cocoa powder.';
        $recipe->setNotes($notes);
        $this->assertEquals($notes, $recipe->getNotes());
        
        // Test nullable notes
        $recipe->setNotes(null);
        $this->assertNull($recipe->getNotes());
    }

    public function testRecipeSteps(): void
    {
        $recipe = new Recipe();
        
        // Test initial empty steps
        $this->assertIsArray($recipe->getSteps());
        $this->assertEmpty($recipe->getSteps());
        
        // Test setting steps
        $steps = [
            'Preheat oven to 350°F',
            'Mix dry ingredients',
            'Add wet ingredients',
            'Bake for 30 minutes'
        ];
        $recipe->setSteps($steps);
        $this->assertEquals($steps, $recipe->getSteps());
        
        // Test adding individual steps
        $recipe->addStep('Let cool before serving');
        $expectedSteps = array_merge($steps, ['Let cool before serving']);
        $this->assertEquals($expectedSteps, $recipe->getSteps());
    }

    public function testRecipeRating(): void
    {
        $recipe = new Recipe();
        
        // Test initial null rating
        $this->assertNull($recipe->getRating());
        
        // Test setting rating
        $recipe->setRating(Rating::FOUR);
        $this->assertEquals(Rating::FOUR, $recipe->getRating());
        
        // Test setting null rating
        $recipe->setRating(null);
        $this->assertNull($recipe->getRating());
        
        // Test all rating values
        foreach (Rating::cases() as $rating) {
            $recipe->setRating($rating);
            $this->assertEquals($rating, $recipe->getRating());
        }
    }

    public function testRecipeIngredients(): void
    {
        $recipe = new Recipe();
        $foodItem = new FoodItem();
        $foodItem->setName('Flour');
        $foodItem->setDescription('All-purpose flour');
        
        $ingredient = new Ingredient();
        $ingredient->setFoodItem($foodItem);
        $ingredient->setQuantity(Quantity::grams(250));
        
        // Test adding ingredient
        $recipe->addIngredient($ingredient);
        $this->assertCount(1, $recipe->getIngredients());
        $this->assertTrue($recipe->getIngredients()->contains($ingredient));
        $this->assertEquals($recipe, $ingredient->getRecipe());
        
        // Test removing ingredient
        $recipe->removeIngredient($ingredient);
        $this->assertCount(0, $recipe->getIngredients());
        $this->assertFalse($recipe->getIngredients()->contains($ingredient));
        $this->assertNull($ingredient->getRecipe());
    }

    public function testRecipeCookingSessions(): void
    {
        $recipe = new Recipe();
        $cookingSession = new CookingSession();
        $cookingSession->setCookedAt(new \DateTimeImmutable('2024-01-15 18:30:00'));
        $cookingSession->setNotes('Turned out great! Used dark chocolate instead of milk chocolate.');
        
        // Test adding cooking session
        $recipe->addCookingSession($cookingSession);
        $this->assertCount(1, $recipe->getCookingSessions());
        $this->assertTrue($recipe->getCookingSessions()->contains($cookingSession));
        $this->assertEquals($recipe, $cookingSession->getRecipe());
        
        // Test removing cooking session
        $recipe->removeCookingSession($cookingSession);
        $this->assertCount(0, $recipe->getCookingSessions());
        $this->assertFalse($recipe->getCookingSessions()->contains($cookingSession));
        $this->assertNull($cookingSession->getRecipe());
    }

    public function testRecipeDishRelationship(): void
    {
        $recipe = new Recipe();
        $dish = new Dish();
        $dish->setName('Chocolate Cake');
        $dish->setDescription('A classic chocolate cake');
        $dish->setDailyMealFriendly(false);
        $dish->setPrepFriendly(true);
        $dish->setFreezerFriendly(true);
        
        // Test setting dish
        $recipe->setDish($dish);
        $this->assertEquals($dish, $recipe->getDish());
        
        // Test setting null dish
        $recipe->setDish(null);
        $this->assertNull($recipe->getDish());
    }

    public function testRecipeComplexScenario(): void
    {
        // Create a complete recipe with all components
        $dish = new Dish();
        $dish->setName('Chocolate Chip Cookies');
        $dish->setDescription('Classic chocolate chip cookies');
        $dish->setDailyMealFriendly(true);
        $dish->setPrepFriendly(true);
        $dish->setFreezerFriendly(false);
        
        $recipe = new Recipe();
        $recipe->setName('Best Chocolate Chip Cookies');
        $recipe->setDescription('A tried and tested recipe for perfect chocolate chip cookies.');
        $recipe->setDish($dish);
        $recipe->setRating(Rating::FIVE);
        $recipe->setNotes('Bake until edges are golden brown.');
        
        // Add steps
        $steps = [
            'Preheat oven to 375°F',
            'Cream butter and sugars',
            'Add eggs and vanilla',
            'Mix in flour mixture',
            'Fold in chocolate chips',
            'Drop onto baking sheet',
            'Bake for 9-11 minutes'
        ];
        $recipe->setSteps($steps);
        
        // Add ingredients
        $flour = new FoodItem();
        $flour->setName('All-Purpose Flour');
        $flour->setDescription('Standard baking flour');
        
        $flourIngredient = new Ingredient();
        $flourIngredient->setFoodItem($flour);
        $flourIngredient->setQuantity(Quantity::grams(280));
        $recipe->addIngredient($flourIngredient);
        
        $chocolate = new FoodItem();
        $chocolate->setName('Chocolate Chips');
        $chocolate->setDescription('Semi-sweet chocolate chips');
        
        $chocolateIngredient = new Ingredient();
        $chocolateIngredient->setFoodItem($chocolate);
        $chocolateIngredient->setQuantity(Quantity::grams(200));
        $recipe->addIngredient($chocolateIngredient);
        
        // Add cooking sessions
        $session1 = new CookingSession();
        $session1->setCookedAt(new \DateTimeImmutable('2024-01-10 19:00:00'));
        $session1->setNotes('Perfect texture! Baked for 10 minutes.');
        $recipe->addCookingSession($session1);
        
        $session2 = new CookingSession();
        $session2->setCookedAt(new \DateTimeImmutable('2024-01-15 20:30:00'));
        $session2->setNotes('Used dark chocolate chips - even better!');
        $recipe->addCookingSession($session2);
        
        // Verify everything is set correctly
        $this->assertEquals('Best Chocolate Chip Cookies', $recipe->getName());
        $this->assertEquals('A tried and tested recipe for perfect chocolate chip cookies.', $recipe->getDescription());
        $this->assertEquals($dish, $recipe->getDish());
        $this->assertEquals(Rating::FIVE, $recipe->getRating());
        $this->assertEquals('Bake until edges are golden brown.', $recipe->getNotes());
        $this->assertEquals($steps, $recipe->getSteps());
        $this->assertCount(2, $recipe->getIngredients());
        $this->assertCount(2, $recipe->getCookingSessions());
        
        // Verify ingredient details
        $ingredients = $recipe->getIngredients()->toArray();
        $this->assertEquals('All-Purpose Flour', $ingredients[0]->getFoodItem()->getName());
        $this->assertEquals('Chocolate Chips', $ingredients[1]->getFoodItem()->getName());
        
        // Verify cooking session details
        $sessions = $recipe->getCookingSessions()->toArray();
        $this->assertEquals('2024-01-10 19:00:00', $sessions[0]->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Perfect texture! Baked for 10 minutes.', $sessions[0]->getNotes());
        $this->assertEquals('2024-01-15 20:30:00', $sessions[1]->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Used dark chocolate chips - even better!', $sessions[1]->getNotes());
    }

    public function testRecipeEmptyCollections(): void
    {
        $recipe = new Recipe();
        
        // Test that collections are initialized as empty
        $this->assertCount(0, $recipe->getIngredients());
        $this->assertCount(0, $recipe->getCookingSessions());
        $this->assertIsArray($recipe->getSteps());
        $this->assertEmpty($recipe->getSteps());
    }

    public function testRecipeBidirectionalRelationships(): void
    {
        $recipe = new Recipe();
        $recipe->setName('Test Recipe');
        $recipe->setDescription('Test Description');
        
        $dish = new Dish();
        $dish->setName('Test Dish');
        $dish->setDescription('Test Dish Description');
        $dish->setDailyMealFriendly(true);
        $dish->setPrepFriendly(false);
        $dish->setFreezerFriendly(false);
        
        // Test bidirectional relationship with dish
        $recipe->setDish($dish);
        $this->assertEquals($dish, $recipe->getDish());
        $this->assertTrue($dish->getRecipes()->contains($recipe));
        
        // Test bidirectional relationship with ingredients
        $foodItem = new FoodItem();
        $foodItem->setName('Test Food');
        $foodItem->setDescription('Test Food Description');
        
        $ingredient = new Ingredient();
        $ingredient->setFoodItem($foodItem);
        $ingredient->setQuantity(Quantity::grams(100));
        
        $recipe->addIngredient($ingredient);
        $this->assertEquals($recipe, $ingredient->getRecipe());
        
        // Test bidirectional relationship with cooking sessions
        $cookingSession = new CookingSession();
        $cookingSession->setCookedAt(new \DateTimeImmutable());
        $cookingSession->setNotes('Test notes');
        
        $recipe->addCookingSession($cookingSession);
        $this->assertEquals($recipe, $cookingSession->getRecipe());
    }
}
