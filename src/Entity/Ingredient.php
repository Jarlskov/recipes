<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['ingredient:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipeVersion().getRecipe().getOwner() == user"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['ingredient:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['ingredient:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['ingredient:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipeVersion().getRecipe().getOwner() == user"
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipeVersion().getRecipe().getOwner() == user"
        )
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ingredient:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ingredient:read', 'ingredient:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Groups(['ingredient:read', 'ingredient:write'])]
    private ?string $amount = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['ingredient:read', 'ingredient:write'])]
    #[Assert\Length(max: 50)]
    private ?string $unit = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ingredient:read', 'ingredient:write'])]
    private ?FullRecipeVersion $recipeVersion = null;

    #[ORM\Column]
    #[Groups(['ingredient:read', 'ingredient:write'])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $orderNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
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

    public function getRecipeVersion(): ?FullRecipeVersion
    {
        return $this->recipeVersion;
    }

    public function setRecipeVersion(?FullRecipeVersion $recipeVersion): static
    {
        $this->recipeVersion = $recipeVersion;
        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(int $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getDisplayText(): string
    {
        $text = '';
        
        if ($this->amount) {
            $text .= $this->amount;
        }
        
        if ($this->unit) {
            $text .= ' ' . $this->unit;
        }
        
        if ($this->name) {
            $text .= ' ' . $this->name;
        }
        
        return trim($text);
    }
} 