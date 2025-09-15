<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'food_items')]
class FoodItem
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\Column(type: 'string', length: 255)]
	private string $name;

	#[ORM\Column(type: Types::TEXT)]
	private string $description;

	#[ORM\OneToOne(inversedBy: 'foodItem', targetEntity: Dish::class)]
	#[ORM\JoinColumn(name: 'dish_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
	private ?Dish $dish = null;

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

	public function getDish(): ?Dish
	{
		return $this->dish;
	}

	public function setDish(?Dish $dish): self
	{
		$this->dish = $dish;
		return $this;
	}
}
