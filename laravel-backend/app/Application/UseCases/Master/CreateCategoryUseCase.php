<?php

namespace App\Application\UseCases\Master;

use App\Application\DTOs\Master\CreateCategoryDTO;
use App\Domain\Master\Entities\Category;
use App\Domain\Master\Repositories\CategoryRepositoryInterface;
use App\Domain\Master\Services\CategoryDomainService;

class CreateCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly CategoryDomainService $domainService,
    ) {}

    public function execute(CreateCategoryDTO $dto): Category
    {
        $this->domainService->ensureNameIsUnique($dto->name);

        $category = Category::create(
            name: $dto->name,
            title: $dto->title,
            description: $dto->description,
            status: $dto->status,
        );

        return $this->repository->save($category);
    }
}
