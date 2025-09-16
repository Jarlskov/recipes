<?php

namespace App\Tests\Entity;

use App\Entity\CookingSession;
use App\Entity\Recipe;
use App\Entity\Dish;
use PHPUnit\Framework\TestCase;

class CookingSessionTest extends TestCase
{
    public function testCookingSessionBasicFields(): void
    {
        $cookingSession = new CookingSession();
        
        // Test cookedAt
        $cookedAt = new \DateTimeImmutable('2024-01-15 18:30:00');
        $cookingSession->setCookedAt($cookedAt);
        $this->assertEquals($cookedAt, $cookingSession->getCookedAt());
        
        // Test notes
        $notes = 'Turned out great! Used dark chocolate instead of milk chocolate.';
        $cookingSession->setNotes($notes);
        $this->assertEquals($notes, $cookingSession->getNotes());
        
        // Test nullable notes
        $cookingSession->setNotes(null);
        $this->assertNull($cookingSession->getNotes());
    }

    public function testCookingSessionRecipeRelationship(): void
    {
        $cookingSession = new CookingSession();
        $cookingSession->setCookedAt(new \DateTimeImmutable('2024-01-15 18:30:00'));
        $cookingSession->setNotes('Test cooking session');
        
        $dish = new Dish();
        $dish->setName('Chocolate Cake');
        $dish->setDescription('A classic chocolate cake');
        $dish->setDailyMealFriendly(false);
        $dish->setPrepFriendly(true);
        $dish->setFreezerFriendly(true);
        
        $recipe = new Recipe();
        $recipe->setName('Best Chocolate Cake');
        $recipe->setDescription('A delicious chocolate cake recipe');
        $recipe->setDish($dish);
        
        // Test setting recipe
        $cookingSession->setRecipe($recipe);
        $this->assertEquals($recipe, $cookingSession->getRecipe());
        
        // Test setting null recipe
        $cookingSession->setRecipe(null);
        $this->assertNull($cookingSession->getRecipe());
    }

    public function testCookingSessionBidirectionalRelationship(): void
    {
        $cookingSession = new CookingSession();
        $cookingSession->setCookedAt(new \DateTimeImmutable('2024-01-15 18:30:00'));
        $cookingSession->setNotes('Test notes');
        
        $dish = new Dish();
        $dish->setName('Test Dish');
        $dish->setDescription('Test Dish Description');
        $dish->setDailyMealFriendly(true);
        $dish->setPrepFriendly(false);
        $dish->setFreezerFriendly(false);
        
        $recipe = new Recipe();
        $recipe->setName('Test Recipe');
        $recipe->setDescription('Test Recipe Description');
        $recipe->setDish($dish);
        
        // Test bidirectional relationship
        $recipe->addCookingSession($cookingSession);
        $this->assertEquals($recipe, $cookingSession->getRecipe());
        $this->assertTrue($recipe->getCookingSessions()->contains($cookingSession));
        
        // Test removing from recipe
        $recipe->removeCookingSession($cookingSession);
        $this->assertNull($cookingSession->getRecipe());
        $this->assertFalse($recipe->getCookingSessions()->contains($cookingSession));
    }

    public function testCookingSessionMultipleSessions(): void
    {
        $dish = new Dish();
        $dish->setName('Chocolate Chip Cookies');
        $dish->setDescription('Classic chocolate chip cookies');
        $dish->setDailyMealFriendly(true);
        $dish->setPrepFriendly(true);
        $dish->setFreezerFriendly(false);
        
        $recipe = new Recipe();
        $recipe->setName('Best Chocolate Chip Cookies');
        $recipe->setDescription('A tried and tested recipe');
        $recipe->setDish($dish);
        
        // Create multiple cooking sessions
        $session1 = new CookingSession();
        $session1->setCookedAt(new \DateTimeImmutable('2024-01-10 19:00:00'));
        $session1->setNotes('Perfect texture! Baked for 10 minutes.');
        
        $session2 = new CookingSession();
        $session2->setCookedAt(new \DateTimeImmutable('2024-01-15 20:30:00'));
        $session2->setNotes('Used dark chocolate chips - even better!');
        
        $session3 = new CookingSession();
        $session3->setCookedAt(new \DateTimeImmutable('2024-01-20 17:45:00'));
        $session3->setNotes('Made double batch - froze half for later.');
        
        // Add all sessions to recipe
        $recipe->addCookingSession($session1);
        $recipe->addCookingSession($session2);
        $recipe->addCookingSession($session3);
        
        // Verify all sessions are associated
        $this->assertCount(3, $recipe->getCookingSessions());
        $this->assertTrue($recipe->getCookingSessions()->contains($session1));
        $this->assertTrue($recipe->getCookingSessions()->contains($session2));
        $this->assertTrue($recipe->getCookingSessions()->contains($session3));
        
        // Verify bidirectional relationships
        $this->assertEquals($recipe, $session1->getRecipe());
        $this->assertEquals($recipe, $session2->getRecipe());
        $this->assertEquals($recipe, $session3->getRecipe());
        
        // Verify individual session data
        $this->assertEquals('2024-01-10 19:00:00', $session1->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Perfect texture! Baked for 10 minutes.', $session1->getNotes());
        
        $this->assertEquals('2024-01-15 20:30:00', $session2->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Used dark chocolate chips - even better!', $session2->getNotes());
        
        $this->assertEquals('2024-01-20 17:45:00', $session3->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Made double batch - froze half for later.', $session3->getNotes());
    }

    public function testCookingSessionDateTimeHandling(): void
    {
        $cookingSession = new CookingSession();
        
        // Test different date formats
        $dates = [
            new \DateTimeImmutable('2024-01-01 00:00:00'),
            new \DateTimeImmutable('2024-12-31 23:59:59'),
            new \DateTimeImmutable('2023-06-15 12:30:45'),
            new \DateTimeImmutable('2025-03-20 08:15:30')
        ];
        
        foreach ($dates as $date) {
            $cookingSession->setCookedAt($date);
            $this->assertEquals($date, $cookingSession->getCookedAt());
            $this->assertEquals($date->format('Y-m-d H:i:s'), $cookingSession->getCookedAt()->format('Y-m-d H:i:s'));
        }
    }

    public function testCookingSessionNotesVariations(): void
    {
        $cookingSession = new CookingSession();
        $cookingSession->setCookedAt(new \DateTimeImmutable('2024-01-15 18:30:00'));
        
        // Test different types of notes
        $notesVariations = [
            'Simple note',
            'Note with numbers: 123',
            'Note with special chars: !@#$%^&*()',
            'Note with newlines:\nLine 2\nLine 3',
            'Very long note that contains a lot of text and details about the cooking process, including what went well, what could be improved, and any modifications made to the original recipe.',
            '', // Empty string
            null // Null
        ];
        
        foreach ($notesVariations as $notes) {
            $cookingSession->setNotes($notes);
            $this->assertEquals($notes, $cookingSession->getNotes());
        }
    }

    public function testCookingSessionEdgeCases(): void
    {
        $cookingSession = new CookingSession();
        
        // Test that ID is initially null
        $this->assertNull($cookingSession->getId());
        
        // Test that cookedAt is initially null
        $this->assertNull($cookingSession->getCookedAt());
        
        // Test that notes is initially null
        $this->assertNull($cookingSession->getNotes());
        
        // Test that recipe is initially null
        $this->assertNull($cookingSession->getRecipe());
    }

    public function testCookingSessionComplexScenario(): void
    {
        // Create a complete scenario with dish, recipe, and multiple cooking sessions
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
        
        // Create cooking sessions with different outcomes
        $successfulSession = new CookingSession();
        $successfulSession->setCookedAt(new \DateTimeImmutable('2024-01-10 19:00:00'));
        $successfulSession->setNotes('Perfect texture! Baked for 10 minutes. Cookies were crispy on the outside and chewy on the inside.');
        
        $experimentalSession = new CookingSession();
        $experimentalSession->setCookedAt(new \DateTimeImmutable('2024-01-15 20:30:00'));
        $experimentalSession->setNotes('Used dark chocolate chips instead of semi-sweet. Also added a pinch of sea salt. Result was amazing!');
        
        $batchSession = new CookingSession();
        $batchSession->setCookedAt(new \DateTimeImmutable('2024-01-20 17:45:00'));
        $batchSession->setNotes('Made double batch - froze half for later. Used cookie scoop for consistent sizing. Perfect for parties!');
        
        // Add all sessions to recipe
        $recipe->addCookingSession($successfulSession);
        $recipe->addCookingSession($experimentalSession);
        $recipe->addCookingSession($batchSession);
        
        // Verify the complete setup
        $this->assertCount(3, $recipe->getCookingSessions());
        
        $sessions = $recipe->getCookingSessions()->toArray();
        
        // Verify first session
        $this->assertEquals('2024-01-10 19:00:00', $sessions[0]->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Perfect texture! Baked for 10 minutes. Cookies were crispy on the outside and chewy on the inside.', $sessions[0]->getNotes());
        
        // Verify second session
        $this->assertEquals('2024-01-15 20:30:00', $sessions[1]->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Used dark chocolate chips instead of semi-sweet. Also added a pinch of sea salt. Result was amazing!', $sessions[1]->getNotes());
        
        // Verify third session
        $this->assertEquals('2024-01-20 17:45:00', $sessions[2]->getCookedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('Made double batch - froze half for later. Used cookie scoop for consistent sizing. Perfect for parties!', $sessions[2]->getNotes());
        
        // Verify all sessions are properly linked to recipe
        foreach ($sessions as $session) {
            $this->assertEquals($recipe, $session->getRecipe());
        }
    }
}
