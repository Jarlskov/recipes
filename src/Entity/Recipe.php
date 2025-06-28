<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['recipe:read', 'recipe:read:detail']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getOwner() == user"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['recipe:read']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['recipe:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Put(
            denormalizationContext: ['groups' => ['recipe:write']],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getOwner() == user"
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getOwner() == user"
        )
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recipe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read', 'recipe:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['recipe:read:detail'])]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: RecipeVersion::class, orphanRemoval: true)]
    #[Groups(['recipe:read:detail'])]
    private Collection $versions;

    #[ORM\Column]
    #[Groups(['recipe:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['recipe:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, RecipeVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(RecipeVersion $version): static
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setRecipe($this);
        }

        return $this;
    }

    public function removeVersion(RecipeVersion $version): static
    {
        if ($this->versions->removeElement($version)) {
            // set the owning side to null (unless already changed)
            if ($version->getRecipe() === $this) {
                $version->setRecipe(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getLatestVersion(): ?RecipeVersion
    {
        return $this->versions->last() ?: null;
    }
} 