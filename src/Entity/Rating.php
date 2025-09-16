<?php

declare(strict_types=1);

namespace App\Entity;

enum Rating: int
{
    case ONE = 1;
    case TWO = 2;
    case THREE = 3;
    case FOUR = 4;
    case FIVE = 5;

    /**
     * Get all possible rating values
     */
    public static function getValues(): array
    {
        return array_map(fn(self $rating) => $rating->value, self::cases());
    }

    /**
     * Get the minimum rating value
     */
    public static function getMin(): int
    {
        return self::ONE->value;
    }

    /**
     * Get the maximum rating value
     */
    public static function getMax(): int
    {
        return self::FIVE->value;
    }

    /**
     * Create a Rating from an integer value
     */
    public static function fromInt(int $value): self
    {
        return match ($value) {
            1 => self::ONE,
            2 => self::TWO,
            3 => self::THREE,
            4 => self::FOUR,
            5 => self::FIVE,
            default => throw new \InvalidArgumentException(sprintf('Invalid rating value: %d. Must be between 1 and 5.', $value))
        };
    }

    /**
     * Get a human-readable string representation
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ONE => 'Very Poor',
            self::TWO => 'Poor',
            self::THREE => 'Average',
            self::FOUR => 'Good',
            self::FIVE => 'Excellent',
        };
    }

}
