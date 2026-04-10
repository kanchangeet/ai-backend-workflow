<?php

namespace App\Domain\Master\Services;

use App\Domain\Master\Entities\Category;
use App\Domain\Master\Repositories\CategoryRepositoryInterface;

class CategoryDomainService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function ensureNameIsUnique(string $name, ?int $excludeId = null): void
    {
        $categories = $this->repository->findAll();

        $exists = $categories->first(
            fn(Category $c) => $c->name === $name && $c->id !== $excludeId
        );

        if ($exists) {
            throw new \DomainException("Category name '{$name}' already exists.");
        }
    }
}
