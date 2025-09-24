<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'dishes')]
class Dish
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\Column(type: 'string', length: 255)]
	private string $name;

	#[ORM\Column(type: Types::TEXT)]
	private string $description;

	#[ORM\Column(type: 'boolean')]
	private bool $dailyMealFriendly = false;

	#[ORM\Column(type: 'boolean')]
	private bool $prepFriendly = false;

	#[ORM\Column(type: 'boolean')]
	private bool $freezerFriendly = false;

	/** @var Collection<int, RecipeLink> */
	#[ORM\OneToMany(mappedBy: 'dish', targetEntity: RecipeLink::class, cascade: ['persist'], orphanRemoval: true)]
	private Collection $recipeLinks;

	#[ORM\OneToOne(mappedBy: 'dish', targetEntity: FoodItem::class, cascade: ['persist', 'remove'])]
	private ?FoodItem $foodItem = null;

	/** @var Collection<int, Recipe> */
	#[ORM\OneToMany(mappedBy: 'dish', targetEntity: Recipe::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
	private Collection $recipes;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $owner = null;

	public function __construct()
	{
		$this->recipeLinks = new ArrayCollection();
		$this->recipes = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public function isDailyMealFriendly(): bool
	{
		return $this->dailyMealFriendly;
	}

	public function setDailyMealFriendly(bool $dailyMealFriendly): self
	{
		$this->dailyMealFriendly = $dailyMealFriendly;
		return $this;
	}

	public function isPrepFriendly(): bool
	{
		return $this->prepFriendly;
	}

	public function setPrepFriendly(bool $prepFriendly): self
	{
		$this->prepFriendly = $prepFriendly;
		return $this;
	}

	public function isFreezerFriendly(): bool
	{
		return $this->freezerFriendly;
	}

	public function setFreezerFriendly(bool $freezerFriendly): self
	{
		$this->freezerFriendly = $freezerFriendly;
		return $this;
	}

	/**
	 * @return Collection<int, RecipeLink>
	 */
	public function getRecipeLinks(): Collection
	{
		return $this->recipeLinks;
	}

	public function addRecipeLink(RecipeLink $recipeLink): self
	{
		if ($recipeLink->getDish() !== $this) {
			throw new \LogicException('RecipeLink belongs to a different Dish. Create a new RecipeLink for this Dish.');
		}
		if (!$this->recipeLinks->contains($recipeLink)) {
			$this->recipeLinks->add($recipeLink);
		}
		return $this;
	}

	public function removeRecipeLink(RecipeLink $recipeLink): self
	{
		$this->recipeLinks->removeElement($recipeLink);
		return $this;
	}

	public function getFoodItem(): ?FoodItem
	{
		return $this->foodItem;
	}

	public function setFoodItem(?FoodItem $foodItem): self
	{
		$this->foodItem = $foodItem;
		return $this;
	}

	/**
	 * @return Collection<int, Recipe>
	 */
	public function getRecipes(): Collection
	{
		return $this->recipes;
	}

	public function addRecipe(Recipe $recipe): self
	{
		if (!$this->recipes->contains($recipe)) {
			$this->recipes->add($recipe);
			$recipe->setDish($this);
		}
		return $this;
	}

	public function removeRecipe(Recipe $recipe): self
	{
		if ($this->recipes->removeElement($recipe)) {
			if ($recipe->getDish() === $this) {
				$recipe->setDish(null);
			}
		}
		return $this;
	}

	public function getOwner(): ?User
	{
		return $this->owner;
	}

	public function setOwner(?User $owner): self
	{
		$this->owner = $owner;
		return $this;
	}
}
