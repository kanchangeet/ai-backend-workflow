<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Master\Entities\Category;
use App\Domain\Master\Enums\MasterStatus;
use App\Domain\Master\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\CategoryModel;
use Illuminate\Support\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function findById(int $id): ?Category
    {
        $model = CategoryModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(): Collection
    {
        return CategoryModel::all()->map(fn($m) => $this->toDomain($m));
    }

    public function save(Category $category): Category
    {
        $model = new CategoryModel();
        $model->fill($this->toArray($category))->save();

        return $this->toDomain($model);
    }

    public function update(Category $category): Category
    {
        $model = CategoryModel::findOrFail($category->id);
        $model->fill($this->toArray($category))->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        CategoryModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return CategoryModel::where('id', $id)->exists();
    }

    private function toArray(Category $category): array
    {
        return [
            'name'        => $category->name,
            'title'       => $category->title,
            'description' => $category->description,
            'status'      => $category->status->value,
        ];
    }

    private function toDomain(CategoryModel $model): Category
    {
        return new Category(
            id: $model->id,
            name: $model->name,
            title: $model->title,
            description: $model->description,
            status: MasterStatus::from($model->status->value),
            createdAt: $model->created_at?->toIso8601String(),
            updatedAt: $model->updated_at?->toIso8601String(),
        );
    }
}
