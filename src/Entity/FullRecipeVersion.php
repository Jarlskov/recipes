<?php

namespace App\Entity;

use App\Repository\FullRecipeVersionRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FullRecipeVersionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['full_recipe_version:read', 'full_recipe_version:read:detail']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['full_recipe_version:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['full_recipe_version:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['full_recipe_version:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        )
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class FullRecipeVersion extends RecipeVersion
{
    #[ORM\Column(length: 255)]
    #[Groups(['full_recipe_version:read', 'full_recipe_version:write', 'recipe_version:read', 'recipe_version:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $recipeName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['full_recipe_version:read', 'full_recipe_version:write', 'recipe_version:read:detail'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'recipeVersion', targetEntity: Ingredient::class, orphanRemoval: true)]
    #[Groups(['full_recipe_version:read:detail', 'recipe_version:read:detail'])]
    private Collection $ingredients;

    #[ORM\OneToMany(mappedBy: 'recipeVersion', targetEntity: Step::class, orphanRemoval: true)]
    #[ORM\OrderBy(['orderNumber' => 'ASC'])]
    #[Groups(['full_recipe_version:read:detail', 'recipe_version:read:detail'])]
    private Collection $steps;

    public function __construct()
    {
        parent::__construct();
        $this->ingredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
    }

    public function getRecipeName(): ?string
    {
        return $this->recipeName;
    }

    public function setRecipeName(string $recipeName): static
    {
        $this->recipeName = $recipeName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
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
            $ingredient->setRecipeVersion($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipeVersion() === $this) {
                $ingredient->setRecipeVersion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setRecipeVersion($this);
        }

        return $this;
    }

    public function removeStep(Step $step): static
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getRecipeVersion() === $this) {
                $step->setRecipeVersion(null);
            }
        }

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->recipeName ?? 'Full Recipe';
    }
} 