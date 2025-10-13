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
    #[ORM\JoinColumn(nullable: true)]
    private ?FoodItem $foodItem = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $foodItemName = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $amount = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
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

    public function getFoodItemName(): ?string
    {
        return $this->foodItemName;
    }

    public function setFoodItemName(?string $foodItemName): static
    {
        $this->foodItemName = $foodItemName;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;

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
