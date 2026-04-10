<?php

namespace App\Application\UseCases\Master;

use App\Application\DTOs\Master\UpdateCategoryDTO;
use App\Domain\Master\Entities\Category;
use App\Domain\Master\Repositories\CategoryRepositoryInterface;
use App\Domain\Master\Services\CategoryDomainService;

class UpdateCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly CategoryDomainService $domainService,
    ) {}

    public function execute(UpdateCategoryDTO $dto): Category
    {
        $category = $this->repository->findById($dto->id);

        if (! $category) {
            throw new \DomainException("Category not found.");
        }

        $this->domainService->ensureNameIsUnique($dto->name, excludeId: $dto->id);

        $updated = $category->update(
            name: $dto->name,
            title: $dto->title,
            description: $dto->description,
            status: $dto->status,
        );

        return $this->repository->update($updated);
    }
}
