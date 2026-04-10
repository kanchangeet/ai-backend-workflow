<?php

namespace App\Domain\Master\Events;

use App\Domain\Master\Entities\Category;

/**
 * Pure domain event — no framework dependency.
 * Raised when a Category is successfully created.
 */
final class CategoryCreated
{
    public function __construct(
        public readonly Category $category,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}

    public static function raise(Category $category): self
    {
        return new self(
            category: $category,
            occurredAt: new \DateTimeImmutable(),
        );
    }
}
