<?php

namespace App\Application\UseCases\Master;

use App\Domain\Master\Entities\Category;
use App\Domain\Master\Repositories\CategoryRepositoryInterface;

class ShowCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function execute(int $id): Category
    {
        $category = $this->repository->findById($id);

        if (! $category) {
            throw new \DomainException("Category not found.");
        }

        return $category;
    }
}
