<?php

namespace App\Entity;

use App\Repository\BookRecipeVersionRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRecipeVersionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['book_recipe_version:read', 'book_recipe_version:read:detail']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['book_recipe_version:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['book_recipe_version:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['book_recipe_version:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        )
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class BookRecipeVersion extends RecipeVersion
{
    #[ORM\Column(length: 255)]
    #[Groups(['book_recipe_version:read', 'book_recipe_version:write', 'recipe_version:read', 'recipe_version:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $recipeName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book_recipe_version:read', 'book_recipe_version:write', 'recipe_version:read:detail'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $bookTitle = null;

    #[ORM\Column(length: 255)]
    #[Groups(['book_recipe_version:read', 'book_recipe_version:write', 'recipe_version:read:detail'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['book_recipe_version:read', 'book_recipe_version:write', 'recipe_version:read:detail'])]
    #[Assert\Length(max: 255)]
    private ?string $pageNumber = null;

    public function getRecipeName(): ?string
    {
        return $this->recipeName;
    }

    public function setRecipeName(string $recipeName): static
    {
        $this->recipeName = $recipeName;
        return $this;
    }

    public function getBookTitle(): ?string
    {
        return $this->bookTitle;
    }

    public function setBookTitle(string $bookTitle): static
    {
        $this->bookTitle = $bookTitle;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getPageNumber(): ?string
    {
        return $this->pageNumber;
    }

    public function setPageNumber(?string $pageNumber): static
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    public function getDisplayName(): string
    {
        $display = sprintf('%s from "%s" by %s', 
            $this->recipeName ?? 'Recipe', 
            $this->bookTitle ?? 'Unknown Book', 
            $this->author ?? 'Unknown'
        );
        
        if ($this->pageNumber) {
            $display .= sprintf(' (p. %s)', $this->pageNumber);
        }
        
        return $display;
    }
} 