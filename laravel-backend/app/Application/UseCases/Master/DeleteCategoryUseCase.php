<?php

namespace App\Application\UseCases\Master;

use App\Domain\Master\Repositories\CategoryRepositoryInterface;

class DeleteCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function execute(int $id): void
    {
        if (! $this->repository->exists($id)) {
            throw new \DomainException("Category not found.");
        }

        $this->repository->delete($id);
    }
}
