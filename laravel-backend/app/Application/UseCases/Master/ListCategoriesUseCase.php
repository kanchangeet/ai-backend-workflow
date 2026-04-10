<?php

namespace App\Application\UseCases\Master;

use App\Domain\Master\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class ListCategoriesUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
