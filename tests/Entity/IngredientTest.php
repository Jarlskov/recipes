<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\FoodItem;
use App\Entity\Ingredient;
use App\Service\Quantity;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;
use PHPUnit\Framework\TestCase;

class IngredientTest extends TestCase
{
    private function createFoodItem(): FoodItem
    {
        $foodItem = new FoodItem();
        $foodItem->setName('Flour');
        $foodItem->setDescription('All-purpose flour');
        return $foodItem;
    }

    public function testBasicGettersAndSetters(): void
    {
        $ingredient = new Ingredient();
        $foodItem = $this->createFoodItem();

        $ingredient->setFoodItem($foodItem);
        $ingredient->setQuantity(Quantity::grams(500.0));

        $this->assertEquals($foodItem, $ingredient->getFoodItem());
        
        $quantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Mass::class, $quantity);
        $this->assertEquals(500.0, $quantity->toUnit('g'));
    }

    public function testGetQuantityWithMass(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setQuantity(Quantity::grams(500.0));

        $quantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Mass::class, $quantity);
        $this->assertEquals(500.0, $quantity->toUnit('g'));
    }

    public function testGetQuantityWithVolume(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setQuantity(Quantity::milliliters(250.0));

        $quantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Volume::class, $quantity);
        $this->assertEqualsWithDelta(250.0, $quantity->toUnit('ml'), 0.001);
    }

    public function testGetQuantityWithCookingUnits(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setQuantity(Quantity::teaspoons(3.0));

        $quantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Volume::class, $quantity);
        $this->assertEquals(3.0, $quantity->toUnit('tsp'));
    }

    public function testGetQuantityReturnsNullWhenIncomplete(): void
    {
        $ingredient = new Ingredient();

        // No quantity set
        $this->assertNull($ingredient->getQuantity());
    }

    public function testSetQuantityWithMass(): void
    {
        $ingredient = new Ingredient();
        $mass = Quantity::grams(500);

        $ingredient->setQuantity($mass);

        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Mass::class, $retrievedQuantity);
        $this->assertEquals(500.0, $retrievedQuantity->toUnit('g'));
    }

    public function testSetQuantityWithVolume(): void
    {
        $ingredient = new Ingredient();
        $volume = Quantity::milliliters(250);

        $ingredient->setQuantity($volume);

        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Volume::class, $retrievedQuantity);
        $this->assertEqualsWithDelta(250.0, $retrievedQuantity->toUnit('ml'), 0.001);
    }

    public function testSetQuantityPreservesCookingUnits(): void
    {
        $ingredient = new Ingredient();
        $volume = Quantity::teaspoons(3);

        $ingredient->setQuantity($volume);

        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Volume::class, $retrievedQuantity);
        $this->assertEquals(3.0, $retrievedQuantity->toUnit('tsp'));
    }

    public function testQuantityServiceIntegration(): void
    {
        $ingredient = new Ingredient();
        $foodItem = $this->createFoodItem();
        $ingredient->setFoodItem($foodItem);

        $supportedUnits = Quantity::getSupportedUnits();

        foreach ($supportedUnits as $unit) {
            $quantity = Quantity::createFromAmountAndUnit(1.0, $unit);
            $ingredient->setQuantity($quantity);

            $retrievedQuantity = $ingredient->getQuantity();
            $this->assertNotNull($retrievedQuantity, "Unit {$unit} should create a valid quantity");
            $this->assertTrue(
                $retrievedQuantity instanceof Mass || $retrievedQuantity instanceof Volume,
                "Unit {$unit} should create either Mass or Volume"
            );
        }
    }

    public function testIngredientRejectsUnsupportedUnits(): void
    {
        $ingredient = new Ingredient();
        
        // Test that unsupported units are rejected when creating quantities
        $unsupportedUnits = ['oz', 'lb', 'cup', 'pint', 'quart', 'gallon', 'invalid_unit'];
        
        foreach ($unsupportedUnits as $unsupportedUnit) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage("Invalid unit: {$unsupportedUnit}");
            
            // This should throw an exception because the unit is not supported
            Quantity::createFromAmountAndUnit(1.0, $unsupportedUnit);
        }
    }

    public function testIngredientValidatesUnitsThroughQuantityService(): void
    {
        $ingredient = new Ingredient();
        
        // Test that the Ingredient entity properly delegates unit validation to Quantity service
        // by ensuring that only supported units can be used to create quantities
        
        $supportedUnits = Quantity::getSupportedUnits();
        foreach ($supportedUnits as $unit) {
            // This should not throw an exception
            $quantity = Quantity::createFromAmountAndUnit(1.0, $unit);
            $ingredient->setQuantity($quantity);
            
            $retrievedQuantity = $ingredient->getQuantity();
            $this->assertNotNull($retrievedQuantity, "Supported unit {$unit} should work");
        }
        
        // Test that unsupported units are caught at the Quantity service level
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid unit: oz');
        Quantity::createFromAmountAndUnit(1.0, 'oz');
    }

    public function testIngredientHandlesNullUnit(): void
    {
        $ingredient = new Ingredient();
        
        // Test that empty unit is rejected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit cannot be empty');
        Quantity::createFromAmountAndUnit(500, '');
    }

    public function testIngredientRequiresPositiveAmounts(): void
    {
        $ingredient = new Ingredient();
        
        // Test that zero amounts are rejected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive, got: 0');
        Quantity::createFromAmountAndUnit(0, 'g');
        
        // Test that negative amounts are rejected
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive, got: -100');
        Quantity::createFromAmountAndUnit(-100, 'g');
        
        // Test that positive amounts work
        $positiveQuantity = Quantity::createFromAmountAndUnit(100, 'g');
        $ingredient->setQuantity($positiveQuantity);
        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Mass::class, $retrievedQuantity);
        $this->assertEquals(100, $retrievedQuantity->toUnit('g'));
    }

    public function testIngredientHandlesEdgeCaseAmounts(): void
    {
        $ingredient = new Ingredient();
        
        // Test very small positive amounts
        $smallQuantity = Quantity::createFromAmountAndUnit(0.001, 'g');
        $ingredient->setQuantity($smallQuantity);
        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Mass::class, $retrievedQuantity);
        $this->assertEqualsWithDelta(0.001, $retrievedQuantity->toUnit('g'), 0.0001);
        
        // Test very large amounts
        $largeQuantity = Quantity::createFromAmountAndUnit(1000, 'kg');
        $ingredient->setQuantity($largeQuantity);
        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Mass::class, $retrievedQuantity);
        $this->assertEquals(1000, $retrievedQuantity->toUnit('kg'));
        
        // Test decimal amounts
        $decimalQuantity = Quantity::createFromAmountAndUnit(250.75, 'ml');
        $ingredient->setQuantity($decimalQuantity);
        $retrievedQuantity = $ingredient->getQuantity();
        $this->assertInstanceOf(Volume::class, $retrievedQuantity);
        $this->assertEqualsWithDelta(250.75, $retrievedQuantity->toUnit('ml'), 0.01);
    }
}
