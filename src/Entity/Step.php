<?php

namespace App\Entity;

use App\Repository\StepRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StepRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['step:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipeVersion().getRecipe().getOwner() == user"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['step:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['step:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['step:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipeVersion().getRecipe().getOwner() == user"
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getRecipeVersion().getRecipe().getOwner() == user"
        )
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class Step
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['step:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['step:read', 'step:write'])]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['step:read', 'step:write'])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $orderNumber = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['step:read', 'step:write'])]
    private ?FullRecipeVersion $recipeVersion = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(int $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
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
} 