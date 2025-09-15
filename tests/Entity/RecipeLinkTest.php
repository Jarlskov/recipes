<?php

namespace App\Tests\Entity;

use App\Entity\Rating;
use App\Entity\RecipeLink;
use PHPUnit\Framework\TestCase;

class RecipeLinkTest extends TestCase
{
	public function testCanSetAndGetBasicFields(): void
	{
		$link = new RecipeLink();
		$link->setUrl('https://example.com/recipe')
			->setName('Best Pancakes')
			->setAuthorName('Jane Doe');

		$this->assertSame('https://example.com/recipe', $link->getUrl());
		$this->assertSame('Best Pancakes', $link->getName());
		$this->assertSame('Jane Doe', $link->getAuthorName());
	}

	public function testRatingEnumConversion(): void
	{
		$link = new RecipeLink();
		$link->setRating(Rating::FOUR);
		$this->assertSame(Rating::FOUR, $link->getRating());
	}

	public function testNullableRating(): void
	{
		$link = new RecipeLink();
		$this->assertNull($link->getRating());
		$link->setRating(null);
		$this->assertNull($link->getRating());
	}

	public function testLifecycleTimestampsAreSet(): void
	{
		$link = new RecipeLink();

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
