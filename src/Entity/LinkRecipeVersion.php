<?php

namespace App\Entity;

use App\Repository\LinkRecipeVersionRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LinkRecipeVersionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['link_recipe_version:read', 'link_recipe_version:read:detail']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['link_recipe_version:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['link_recipe_version:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['link_recipe_version:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipe().getOwner() == user"
        )
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class LinkRecipeVersion extends RecipeVersion
{
    #[ORM\Column(length: 255)]
    #[Groups(['link_recipe_version:read', 'link_recipe_version:write', 'recipe_version:read', 'recipe_version:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $recipeName = null;

    #[ORM\Column(length: 500)]
    #[Groups(['link_recipe_version:read', 'link_recipe_version:write', 'recipe_version:read:detail'])]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Assert\Length(max: 500)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Groups(['link_recipe_version:read', 'link_recipe_version:write', 'recipe_version:read:detail'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $author = null;

    public function getRecipeName(): ?string
    {
        return $this->recipeName;
    }

    public function setRecipeName(string $recipeName): static
    {
        $this->recipeName = $recipeName;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
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

    public function getDisplayName(): string
    {
        return sprintf('%s (by %s)', $this->recipeName ?? 'Link Recipe', $this->author ?? 'Unknown');
    }
} 