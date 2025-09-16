<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[ORM\Table(name: 'ingredients')]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: FoodItem::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?FoodItem $foodItem = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $amount = null;

    #[ORM\Column(type: Types::STRING, length: 10)]
    private ?string $unit = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoodItem(): ?FoodItem
    {
        return $this->foodItem;
    }

    public function setFoodItem(?FoodItem $foodItem): static
    {
        $this->foodItem = $foodItem;

        return $this;
    }

    /**
     * Get the quantity object for this ingredient
     */
    public function getQuantity(): Mass|Volume|null
    {
        if ($this->amount === null || $this->unit === null) {
            return null;
        }

        // Delegate to Quantity service for creation logic
        return \App\Service\Quantity::createFromAmountAndUnit($this->amount, $this->unit);
    }

    /**
     * Set the quantity for this ingredient
     */
    public function setQuantity(Mass|Volume $quantity): static
    {
        // Delegate to Quantity service for conversion logic
        $amountAndUnit = \App\Service\Quantity::convertToAmountAndUnit($quantity);
        
        $this->amount = $amountAndUnit['amount'];
        $this->unit = $amountAndUnit['unit'];

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }
}
