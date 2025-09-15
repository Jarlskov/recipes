<?php

namespace App\Tests\Entity;

use App\Entity\Dish;
use App\Entity\Rating;
use App\Entity\RecipeLink;
use PHPUnit\Framework\TestCase;

class RecipeLinkTest extends TestCase
{
	private function createDish(): Dish
	{
		$dish = new Dish();
		$dish->setName('Dish')->setDescription('Desc');
		return $dish;
	}

	public function testCanSetAndGetBasicFields(): void
	{
		$link = new RecipeLink($this->createDish());
		$link->setUrl('https://example.com/recipe')
			->setName('Best Pancakes')
			->setAuthorName('Jane Doe');

		$this->assertSame('https://example.com/recipe', $link->getUrl());
		$this->assertSame('Best Pancakes', $link->getName());
		$this->assertSame('Jane Doe', $link->getAuthorName());
	}

	public function testRatingEnumConversion(): void
	{
		$link = new RecipeLink($this->createDish());
		$link->setRating(Rating::FOUR);
		$this->assertSame(Rating::FOUR, $link->getRating());
	}

	public function testNullableRating(): void
	{
		$link = new RecipeLink($this->createDish());
		$this->assertNull($link->getRating());
		$link->setRating(null);
		$this->assertNull($link->getRating());
	}

	public function testLifecycleTimestampsAreSet(): void
	{
		$link = new RecipeLink($this->createDish());

		// Simulate Doctrine PrePersist
		$link->setCreatedAtValue();
		$this->assertInstanceOf(\DateTimeImmutable::class, $link->getCreatedAt());
		$this->assertInstanceOf(\DateTimeImmutable::class, $link->getUpdatedAt());

		// Simulate Doctrine PreUpdate
		$firstUpdatedAt = $link->getUpdatedAt();
		sleep(1);
		$link->setUpdatedAtValue();
		$this->assertGreaterThan($firstUpdatedAt, $link->getUpdatedAt());
	}
}
