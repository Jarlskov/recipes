<?php

namespace App\Service;

use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;

/**
 * Unified helper service for all quantities (mass and volume)
 * Provides SI units and cooking measures with smart unit detection
 */
abstract class Quantity
{
    // Supported mass units
    public const MASS_UNITS = [
        'g' => 'grams',
        'kg' => 'kilograms',
    ];

    // Supported volume units
    public const VOLUME_UNITS = [
        'ml' => 'milliliters',
        'cl' => 'centiliters',
        'dl' => 'deciliters',
        'l' => 'liters',
        'tsp' => 'teaspoons',
        'tbsp' => 'tablespoons',
    ];

    // All supported units
    public const SUPPORTED_UNITS = [
        'g' => 'grams',
        'kg' => 'kilograms',
        'ml' => 'milliliters',
        'cl' => 'centiliters',
        'dl' => 'deciliters',
        'l' => 'liters',
        'tsp' => 'teaspoons',
        'tbsp' => 'tablespoons',
    ];

    // Mass creation methods
    public static function grams(float $value): Mass
    {
        return new Mass($value, 'g');
    }

    public static function kilograms(float $value): Mass
    {
        return new Mass($value, 'kg');
    }

    public static function g(float $value): Mass
    {
        return new Mass($value, 'g');
    }

    public static function kg(float $value): Mass
    {
        return new Mass($value, 'kg');
    }

    // Volume creation methods
    public static function milliliters(float $value): Volume
    {
        return new Volume($value, 'ml');
    }

    public static function centiliters(float $value): Volume
    {
        return new Volume($value, 'cl');
    }

    public static function deciliters(float $value): Volume
    {
        return new Volume($value, 'dl');
    }

    public static function liters(float $value): Volume
    {
        return new Volume($value, 'l');
    }

    public static function teaspoons(float $value): Volume
    {
        return new Volume($value, 'tsp');
    }

    public static function tablespoons(float $value): Volume
    {
        return new Volume($value, 'tbsp');
    }

    // Volume shorthand methods
    public static function ml(float $value): Volume
    {
        return new Volume($value, 'ml');
    }

    public static function cl(float $value): Volume
    {
        return new Volume($value, 'cl');
    }

    public static function dl(float $value): Volume
    {
        return new Volume($value, 'dl');
    }

    public static function l(float $value): Volume
    {
        return new Volume($value, 'l');
    }

    public static function tsp(float $value): Volume
    {
        return new Volume($value, 'tsp');
    }

    public static function tbsp(float $value): Volume
    {
        return new Volume($value, 'tbsp');
    }

    // Utility methods
    public static function getSupportedUnits(): array
    {
        return array_keys(self::SUPPORTED_UNITS);
    }

    public static function getSupportedUnitsWithNames(): array
    {
        return self::SUPPORTED_UNITS;
    }

    public static function getMassUnits(): array
    {
        return array_keys(self::MASS_UNITS);
    }

    public static function getVolumeUnits(): array
    {
        return array_keys(self::VOLUME_UNITS);
    }

    public static function isValidUnit(string $unit): bool
    {
        return array_key_exists($unit, self::SUPPORTED_UNITS);
    }

    public static function isMassUnit(string $unit): bool
    {
        return array_key_exists($unit, self::MASS_UNITS);
    }

    public static function isVolumeUnit(string $unit): bool
    {
        return array_key_exists($unit, self::VOLUME_UNITS);
    }

    // Conversion helpers
    public static function toGrams(Mass $mass): float
    {
        return $mass->toUnit('g');
    }

    public static function toKilograms(Mass $mass): float
    {
        return $mass->toUnit('kg');
    }

    public static function toMilliliters(Volume $volume): float
    {
        return $volume->toUnit('ml');
    }

    public static function toCentiliters(Volume $volume): float
    {
        return $volume->toUnit('cl');
    }

    public static function toDeciliters(Volume $volume): float
    {
        return $volume->toUnit('dl');
    }

    public static function toLiters(Volume $volume): float
    {
        return $volume->toUnit('l');
    }

    public static function toTablespoons(Volume $volume): float
    {
        return $volume->toUnit('tbsp');
    }

    public static function toTeaspoons(Volume $volume): float
    {
        return $volume->toUnit('tsp');
    }

    // Formatting helpers
    public static function format(Mass|Volume $quantity, string $preferredUnit, int $precision = 2): string
    {
        $value = $quantity->toUnit($preferredUnit);
        return number_format($value, $precision) . ' ' . $preferredUnit;
    }

    public static function formatSmart(Mass|Volume $quantity, int $precision = 2): string
    {
        if ($quantity instanceof Mass) {
            return self::formatMassSmart($quantity, $precision);
        } else {
            return self::formatVolumeSmart($quantity, $precision);
        }
    }

    // Helper methods for Ingredient entity
    public static function createFromAmountAndUnit(float $amount, string $unit): Mass|Volume
    {
        if ($unit === '') {
            throw new \InvalidArgumentException("Unit cannot be empty");
        }

        if (!self::isValidUnit($unit)) {
            throw new \InvalidArgumentException("Invalid unit: {$unit}");
        }

        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be positive, got: {$amount}");
        }

        if (self::isMassUnit($unit)) {
            return match ($unit) {
                'g' => self::grams($amount),
                'kg' => self::kilograms($amount),
                default => throw new \InvalidArgumentException("Unsupported mass unit: {$unit}"),
            };
        } elseif (self::isVolumeUnit($unit)) {
            return match ($unit) {
                'ml' => self::milliliters($amount),
                'cl' => self::centiliters($amount),
                'dl' => self::deciliters($amount),
                'l' => self::liters($amount),
                'tsp' => self::teaspoons($amount),
                'tbsp' => self::tablespoons($amount),
                default => throw new \InvalidArgumentException("Unsupported volume unit: {$unit}"),
            };
        }

        throw new \InvalidArgumentException("Unknown unit type: {$unit}");
    }

    public static function convertToAmountAndUnit(Mass|Volume $quantity): array
    {
        if ($quantity instanceof Mass) {
            // Try to preserve the original unit if it's a whole number
            $originalUnit = self::getOriginalUnitFromMass($quantity);
            if ($originalUnit) {
                return [
                    'amount' => $quantity->toUnit($originalUnit),
                    'unit' => $originalUnit
                ];
            } else {
                // Default to grams for mass
                return [
                    'amount' => $quantity->toUnit('g'),
                    'unit' => 'g'
                ];
            }
        } elseif ($quantity instanceof Volume) {
            // Try to preserve the original unit if it's a whole number
            $originalUnit = self::getOriginalUnitFromQuantity($quantity);
            if ($originalUnit) {
                return [
                    'amount' => $quantity->toUnit($originalUnit),
                    'unit' => $originalUnit
                ];
            } else {
                // Default to milliliters for volume
                return [
                    'amount' => $quantity->toUnit('ml'),
                    'unit' => 'ml'
                ];
            }
        }

        throw new \InvalidArgumentException("Unknown quantity type");
    }

    /**
     * Helper method to determine the best unit from a mass quantity object
     */
    private static function getOriginalUnitFromMass(Mass $mass): ?string
    {
        // Check if the mass is a whole number in any of our supported mass units
        // Prioritize larger units (kg over g) when both are whole numbers
        $supportedUnits = ['kg', 'g']; // Ordered from largest to smallest
        
        foreach ($supportedUnits as $unit) {
            try {
                $value = $mass->toUnit($unit);
                if (abs($value - round($value)) < 0.001) { // Close to whole number
                    return $unit;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return null;
    }

    /**
     * Helper method to determine the best unit from a volume quantity object
     */
    private static function getOriginalUnitFromQuantity(Volume $volume): ?string
    {
        // Check if the volume is a whole number in any of our supported units
        // Prioritize larger units and cooking units when they result in whole numbers
        $supportedUnits = ['l', 'dl', 'cl', 'ml', 'tbsp', 'tsp']; // Ordered from largest to smallest
        
        foreach ($supportedUnits as $unit) {
            try {
                $value = $volume->toUnit($unit);
                if (abs($value - round($value)) < 0.001) { // Close to whole number
                    return $unit;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return null;
    }

    private static function formatMassSmart(Mass $mass, int $precision): string
    {
        $grams = $mass->toUnit('g');
        
        if ($grams >= 1000) {
            return self::format($mass, 'kg', $precision);
        } else {
            return self::format($mass, 'g', $precision);
        }
    }

    private static function formatVolumeSmart(Volume $volume, int $precision): string
    {
        // Try to determine if this is a cooking unit by checking for whole numbers in tsp/tbsp
        $cookingUnits = ['tsp', 'tbsp'];
        foreach ($cookingUnits as $unit) {
            try {
                $value = $volume->toUnit($unit);
                if (abs($value - round($value)) < 0.001) { // Close to whole number
                    return number_format($value, $precision) . ' ' . $unit;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // If not a cooking unit, use SI formatting
        $ml = $volume->toUnit('ml');
        
        if ($ml >= 1000) {
            return self::format($volume, 'l', $precision);
        } elseif ($ml >= 100) {
            return self::format($volume, 'dl', $precision);
        } elseif ($ml >= 10) {
            return self::format($volume, 'cl', $precision);
        } else {
            return self::format($volume, 'ml', $precision);
        }
    }
}
