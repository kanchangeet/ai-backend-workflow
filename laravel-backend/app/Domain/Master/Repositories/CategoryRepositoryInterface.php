<?php

namespace App\Domain\Master\Repositories;

use App\Domain\Master\Entities\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;

    public function findAll(): Collection;

    public function save(Category $category): Category;

    public function update(Category $category): Category;

    public function delete(int $id): void;

    public function exists(int $id): bool;
}
