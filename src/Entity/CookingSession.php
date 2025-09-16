<?php

namespace App\Entity;

use App\Repository\CookingSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CookingSessionRepository::class)]
#[ORM\Table(name: 'cooking_sessions')]
class CookingSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $cookedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'cookingSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCookedAt(): ?\DateTimeImmutable
    {
        return $this->cookedAt;
    }

    public function setCookedAt(\DateTimeImmutable $cookedAt): static
    {
        $this->cookedAt = $cookedAt;

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
