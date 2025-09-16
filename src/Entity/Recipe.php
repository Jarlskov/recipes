<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: 'recipes')]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Ingredient::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ingredients;

    #[ORM\Column(type: Types::JSON)]
    private array $steps = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: CookingSession::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $cookingSessions;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: Dish::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dish $dish = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->cookingSessions = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    public function addStep(string $step): static
    {
        $this->steps[] = $step;

        return $this;
    }

    public function getRating(): ?Rating
    {
        return $this->rating ? Rating::fromInt($this->rating) : null;
    }

    public function setRating(?Rating $rating): static
    {
        $this->rating = $rating?->value;

        return $this;
    }

    /**
     * @return Collection<int, CookingSession>
     */
    public function getCookingSessions(): Collection
    {
        return $this->cookingSessions;
    }

    public function addCookingSession(CookingSession $cookingSession): static
    {
        if (!$this->cookingSessions->contains($cookingSession)) {
            $this->cookingSessions->add($cookingSession);
            $cookingSession->setRecipe($this);
        }

        return $this;
    }

    public function removeCookingSession(CookingSession $cookingSession): static
    {
        if ($this->cookingSessions->removeElement($cookingSession)) {
            // set the owning side to null (unless already changed)
            if ($cookingSession->getRecipe() === $this) {
                $cookingSession->setRecipe(null);
            }
        }

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): static
    {
        // unset the owning side of the relation if necessary
        if ($dish === null && $this->dish !== null) {
            $this->dish->removeRecipe($this);
        }

        // set the owning side of the relation if necessary
        if ($dish !== null && $dish !== $this->dish) {
            $dish->addRecipe($this);
        }

        $this->dish = $dish;

        return $this;
    }
}
