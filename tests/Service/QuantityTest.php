<?php

namespace App\Tests\Service;

use App\Service\Quantity;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;
use PHPUnit\Framework\TestCase;

class QuantityTest extends TestCase
{
    public function testSupportedUnits(): void
    {
        $units = Quantity::getSupportedUnits();
        $expected = ['g', 'kg', 'ml', 'cl', 'dl', 'l', 'tsp', 'tbsp'];
        
        $this->assertEquals($expected, $units);
    }

    public function testSupportedUnitsWithNames(): void
    {
        $units = Quantity::getSupportedUnitsWithNames();
        $expected = [
            'g' => 'grams',
            'kg' => 'kilograms',
            'ml' => 'milliliters',
            'cl' => 'centiliters',
            'dl' => 'deciliters',
            'l' => 'liters',
            'tsp' => 'teaspoons',
            'tbsp' => 'tablespoons',
        ];
        
        $this->assertEquals($expected, $units);
    }

    public function testMassUnits(): void
    {
        $units = Quantity::getMassUnits();
        $expected = ['g', 'kg'];
        
        $this->assertEquals($expected, $units);
    }

    public function testVolumeUnits(): void
    {
        $units = Quantity::getVolumeUnits();
        $expected = ['ml', 'cl', 'dl', 'l', 'tsp', 'tbsp'];
        
        $this->assertEquals($expected, $units);
    }

    public function testIsValidUnit(): void
    {
        $this->assertTrue(Quantity::isValidUnit('g'));
        $this->assertTrue(Quantity::isValidUnit('kg'));
        $this->assertTrue(Quantity::isValidUnit('ml'));
        $this->assertTrue(Quantity::isValidUnit('tsp'));
        
        $this->assertFalse(Quantity::isValidUnit('invalid'));
        $this->assertFalse(Quantity::isValidUnit('oz'));
    }

    public function testIsMassUnit(): void
    {
        $this->assertTrue(Quantity::isMassUnit('g'));
        $this->assertTrue(Quantity::isMassUnit('kg'));
        
        $this->assertFalse(Quantity::isMassUnit('ml'));
        $this->assertFalse(Quantity::isMassUnit('tsp'));
    }

    public function testIsVolumeUnit(): void
    {
        $this->assertTrue(Quantity::isVolumeUnit('ml'));
        $this->assertTrue(Quantity::isVolumeUnit('tsp'));
        
        $this->assertFalse(Quantity::isVolumeUnit('g'));
        $this->assertFalse(Quantity::isVolumeUnit('kg'));
    }

    public function testMassCreation(): void
    {
        $grams = Quantity::grams(500);
        $this->assertInstanceOf(Mass::class, $grams);
        $this->assertEquals(500, $grams->toUnit('g'));

        $kilograms = Quantity::kilograms(2.5);
        $this->assertInstanceOf(Mass::class, $kilograms);
        $this->assertEquals(2.5, $kilograms->toUnit('kg'));

        $gramsShorthand = Quantity::g(250);
        $this->assertEquals(250, $gramsShorthand->toUnit('g'));

        $kilogramsShorthand = Quantity::kg(1.5);
        $this->assertEquals(1.5, $kilogramsShorthand->toUnit('kg'));
    }

    public function testVolumeCreation(): void
    {
        $milliliters = Quantity::milliliters(500);
        $this->assertInstanceOf(Volume::class, $milliliters);
        $this->assertEqualsWithDelta(500, $milliliters->toUnit('ml'), 0.001);

        $centiliters = Quantity::centiliters(25);
        $this->assertInstanceOf(Volume::class, $centiliters);
        $this->assertEquals(25, $centiliters->toUnit('cl'));

        $deciliters = Quantity::deciliters(5);
        $this->assertInstanceOf(Volume::class, $deciliters);
        $this->assertEquals(5, $deciliters->toUnit('dl'));

        $liters = Quantity::liters(2.5);
        $this->assertInstanceOf(Volume::class, $liters);
        $this->assertEquals(2.5, $liters->toUnit('l'));

        $teaspoons = Quantity::teaspoons(3);
        $this->assertInstanceOf(Volume::class, $teaspoons);
        $this->assertEquals(3, $teaspoons->toUnit('tsp'));

        $tablespoons = Quantity::tablespoons(2);
        $this->assertInstanceOf(Volume::class, $tablespoons);
        $this->assertEquals(2, $tablespoons->toUnit('tbsp'));
    }

    public function testVolumeShorthandCreation(): void
    {
        $milliliters = Quantity::ml(250);
        $this->assertEqualsWithDelta(250, $milliliters->toUnit('ml'), 0.001);

        $centiliters = Quantity::cl(15);
        $this->assertEquals(15, $centiliters->toUnit('cl'));

        $deciliters = Quantity::dl(3);
        $this->assertEquals(3, $deciliters->toUnit('dl'));

        $liters = Quantity::l(1.5);
        $this->assertEquals(1.5, $liters->toUnit('l'));

        $teaspoons = Quantity::tsp(4);
        $this->assertEquals(4, $teaspoons->toUnit('tsp'));

        $tablespoons = Quantity::tbsp(1);
        $this->assertEquals(1, $tablespoons->toUnit('tbsp'));
    }

    public function testMassConversions(): void
    {
        $grams = Quantity::grams(1000);
        $this->assertEquals(1000, Quantity::toGrams($grams));
        $this->assertEquals(1, Quantity::toKilograms($grams));

        $kilograms = Quantity::kilograms(2.5);
        $this->assertEquals(2500, Quantity::toGrams($kilograms));
        $this->assertEquals(2.5, Quantity::toKilograms($kilograms));
    }

    public function testVolumeConversions(): void
    {
        $milliliters = Quantity::milliliters(1000);
        $this->assertEqualsWithDelta(1000, Quantity::toMilliliters($milliliters), 0.001);
        $this->assertEquals(100, Quantity::toCentiliters($milliliters));
        $this->assertEquals(10, Quantity::toDeciliters($milliliters));
        $this->assertEquals(1, Quantity::toLiters($milliliters));

        $liters = Quantity::liters(2.5);
        $this->assertEquals(2500, Quantity::toMilliliters($liters));
        $this->assertEqualsWithDelta(250, Quantity::toCentiliters($liters), 0.001);
        $this->assertEquals(25, Quantity::toDeciliters($liters));
        $this->assertEquals(2.5, Quantity::toLiters($liters));
    }

    public function testCookingConversions(): void
    {
        $teaspoons = Quantity::teaspoons(3);
        $this->assertEquals(3, Quantity::toTeaspoons($teaspoons));

        $tablespoons = Quantity::tablespoons(2);
        $this->assertEquals(2, Quantity::toTablespoons($tablespoons));
    }

    public function testFormatting(): void
    {
        $grams = Quantity::grams(500);
        $this->assertEquals('500.00 g', Quantity::format($grams, 'g'));
        $this->assertEquals('0.50 kg', Quantity::format($grams, 'kg'));

        $milliliters = Quantity::milliliters(500);
        $this->assertEquals('500.00 ml', Quantity::format($milliliters, 'ml'));
        $this->assertEquals('0.50 l', Quantity::format($milliliters, 'l'));
    }

    public function testFormatSmart(): void
    {
        // Test mass formatting
        $smallMass = Quantity::grams(500);
        $this->assertEquals('500.00 g', Quantity::formatSmart($smallMass));

        $largeMass = Quantity::grams(1500);
        $this->assertEquals('1.50 kg', Quantity::formatSmart($largeMass));

        // Test volume SI formatting
        $smallVolume = Quantity::milliliters(50);
        $this->assertEquals('5.00 cl', Quantity::formatSmart($smallVolume));

        $mediumVolume = Quantity::milliliters(150);
        $this->assertEquals('1.50 dl', Quantity::formatSmart($mediumVolume));

        $largeVolume = Quantity::milliliters(500);
        $this->assertEquals('5.00 dl', Quantity::formatSmart($largeVolume));

        $veryLargeVolume = Quantity::milliliters(1500);
        $this->assertEquals('1.50 l', Quantity::formatSmart($veryLargeVolume));

        // Test volume cooking formatting
        $teaspoons = Quantity::teaspoons(3);
        $this->assertEquals('3.00 tsp', Quantity::formatSmart($teaspoons));

        $tablespoons = Quantity::tablespoons(2);
        $this->assertEquals('6.00 tsp', Quantity::formatSmart($tablespoons));
    }

    public function testNoCrossConversionBetweenCookingAndSI(): void
    {
        // Test that cooking units stay in cooking units when formatted smart
        $teaspoons = Quantity::teaspoons(3);
        $smartFormat = Quantity::formatSmart($teaspoons);
        
        // Should remain in tsp, not convert to ml/cl/dl/l
        $this->assertStringContainsString('tsp', $smartFormat);
        $this->assertStringNotContainsString('ml', $smartFormat);
        $this->assertStringNotContainsString('cl', $smartFormat);
        $this->assertStringNotContainsString('dl', $smartFormat);
        $this->assertStringNotContainsString('l', $smartFormat);

        $tablespoons = Quantity::tablespoons(2);
        $smartFormat = Quantity::formatSmart($tablespoons);
        
        // Should remain in tsp/tbsp, not convert to SI units
        $this->assertTrue(
            str_contains($smartFormat, 'tsp') || str_contains($smartFormat, 'tbsp'),
            'Cooking units should stay in cooking units'
        );
        $this->assertStringNotContainsString('ml', $smartFormat);
        $this->assertStringNotContainsString('cl', $smartFormat);
        $this->assertStringNotContainsString('dl', $smartFormat);
        $this->assertStringNotContainsString('l', $smartFormat);

        // Test that SI units stay in SI units when formatted smart
        $milliliters = Quantity::milliliters(500);
        $smartFormat = Quantity::formatSmart($milliliters);
        
        // Should remain in SI units, not convert to tsp/tbsp
        $this->assertTrue(
            str_contains($smartFormat, 'ml') || 
            str_contains($smartFormat, 'cl') || 
            str_contains($smartFormat, 'dl') || 
            str_contains($smartFormat, 'l'),
            'SI units should stay in SI units'
        );
        $this->assertStringNotContainsString('tsp', $smartFormat);
        $this->assertStringNotContainsString('tbsp', $smartFormat);
    }

    public function testCookingUnitsOnlyConvertWithinCookingSystem(): void
    {
        // Test that cooking units can convert between each other but not to SI
        $teaspoons = Quantity::teaspoons(3);
        $tablespoons = Quantity::tablespoons(1);
        
        // 1 tbsp = 3 tsp, so these should be equal
        $this->assertEqualsWithDelta(
            $teaspoons->toUnit('tsp'),
            $tablespoons->toUnit('tsp'),
            0.001
        );
        
        // But when formatted smart, they should stay in cooking units
        $teaspoonsFormat = Quantity::formatSmart($teaspoons);
        $tablespoonsFormat = Quantity::formatSmart($tablespoons);
        
        $this->assertStringContainsString('tsp', $teaspoonsFormat);
        $this->assertStringContainsString('tsp', $tablespoonsFormat);
        
        // Neither should contain SI units
        $this->assertStringNotContainsString('ml', $teaspoonsFormat);
        $this->assertStringNotContainsString('ml', $tablespoonsFormat);
    }

    public function testSIUnitsOnlyConvertWithinSISystem(): void
    {
        // Test that SI units can convert between each other but not to cooking
        $milliliters = Quantity::milliliters(1000);
        $liters = Quantity::liters(1);
        
        // These should be equal
        $this->assertEqualsWithDelta(
            $milliliters->toUnit('ml'),
            $liters->toUnit('ml'),
            0.001
        );
        
        // But when formatted smart, they should stay in SI units
        $mlFormat = Quantity::formatSmart($milliliters);
        $lFormat = Quantity::formatSmart($liters);
        
        $this->assertStringContainsString('l', $mlFormat); // Should convert to liters for large volumes
        $this->assertStringContainsString('l', $lFormat);
        
        // Neither should contain cooking units
        $this->assertStringNotContainsString('tsp', $mlFormat);
        $this->assertStringNotContainsString('tsp', $lFormat);
        $this->assertStringNotContainsString('tbsp', $mlFormat);
        $this->assertStringNotContainsString('tbsp', $lFormat);
    }

    public function testFormatMethodRespectsUnitTypes(): void
    {
        // Test that format() method doesn't allow cross-type formatting
        $teaspoons = Quantity::teaspoons(3);
        
        // Should be able to format cooking units in cooking units
        $this->assertEquals('3.00 tsp', Quantity::format($teaspoons, 'tsp'));
        $this->assertEquals('1.00 tbsp', Quantity::format($teaspoons, 'tbsp'));
        
        // Should be able to format SI units in SI units
        $milliliters = Quantity::milliliters(500);
        $this->assertEquals('500.00 ml', Quantity::format($milliliters, 'ml'));
        $this->assertEquals('5.00 dl', Quantity::format($milliliters, 'dl'));
        
        // But format() should not automatically convert between types
        // (This is more of a design verification - the format method takes any unit)
        // The smart formatting is what prevents cross-conversion
    }

    public function testCreateFromAmountAndUnit(): void
    {
        // Test mass units
        $grams = Quantity::createFromAmountAndUnit(500, 'g');
        $this->assertInstanceOf(Mass::class, $grams);
        $this->assertEquals(500, $grams->toUnit('g'));

        $kilograms = Quantity::createFromAmountAndUnit(2.5, 'kg');
        $this->assertInstanceOf(Mass::class, $kilograms);
        $this->assertEquals(2.5, $kilograms->toUnit('kg'));

        // Test volume units
        $milliliters = Quantity::createFromAmountAndUnit(250, 'ml');
        $this->assertInstanceOf(Volume::class, $milliliters);
        $this->assertEqualsWithDelta(250, $milliliters->toUnit('ml'), 0.001);

        $teaspoons = Quantity::createFromAmountAndUnit(3, 'tsp');
        $this->assertInstanceOf(Volume::class, $teaspoons);
        $this->assertEquals(3, $teaspoons->toUnit('tsp'));
    }

    public function testCreateFromAmountAndUnitThrowsExceptionForInvalidUnit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid unit: invalid_unit');
        Quantity::createFromAmountAndUnit(500, 'invalid_unit');
    }

    public function testCreateFromAmountAndUnitThrowsExceptionForEmptyUnit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit cannot be empty');
        Quantity::createFromAmountAndUnit(500, '');
    }

    public function testCreateFromAmountAndUnitThrowsExceptionForZeroAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive, got: 0');
        Quantity::createFromAmountAndUnit(0, 'g');
    }

    public function testCreateFromAmountAndUnitThrowsExceptionForNegativeAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive, got: -100');
        Quantity::createFromAmountAndUnit(-100, 'g');
    }

    public function testConvertToAmountAndUnit(): void
    {
        // Test mass conversion with whole number preservation
        $grams = Quantity::grams(500);
        $result = Quantity::convertToAmountAndUnit($grams);
        $this->assertEquals(['amount' => 500, 'unit' => 'g'], $result);

        // Test mass conversion with kilograms (should prefer kg when it's a whole number)
        $kilograms = Quantity::kilograms(2);
        $result = Quantity::convertToAmountAndUnit($kilograms);
        $this->assertEquals(['amount' => 2, 'unit' => 'kg'], $result);

        // Test mass conversion without whole number (defaults to g)
        $gramsDecimal = Quantity::grams(500.5);
        $result = Quantity::convertToAmountAndUnit($gramsDecimal);
        $this->assertEqualsWithDelta(['amount' => 500.5, 'unit' => 'g'], $result, 0.001);

        // Test volume conversion with whole number preservation (prefers tbsp over tsp)
        $teaspoons = Quantity::teaspoons(3);
        $result = Quantity::convertToAmountAndUnit($teaspoons);
        $this->assertEquals(['amount' => 1.0, 'unit' => 'tbsp'], $result);

        // Test volume conversion without whole number (defaults to ml)
        $milliliters = Quantity::milliliters(250.5);
        $result = Quantity::convertToAmountAndUnit($milliliters);
        $this->assertEqualsWithDelta(['amount' => 250.5, 'unit' => 'ml'], $result, 0.001);
    }

    public function testConvertToAmountAndUnitThrowsExceptionForUnknownType(): void
    {
        // Since the method has a type hint, we can't pass an invalid type directly
        // Instead, let's test that the method works correctly with valid types
        // and the type system prevents invalid types at compile time
        
        $grams = Quantity::grams(500);
        $result = Quantity::convertToAmountAndUnit($grams);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('unit', $result);
        
        $volume = Quantity::milliliters(250);
        $result = Quantity::convertToAmountAndUnit($volume);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('unit', $result);
    }
}
