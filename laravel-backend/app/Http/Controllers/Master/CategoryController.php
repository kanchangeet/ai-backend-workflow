<?php

namespace App\Http\Controllers\Master;

use App\Application\DTOs\Master\CreateCategoryDTO;
use App\Application\DTOs\Master\UpdateCategoryDTO;
use App\Application\UseCases\Master\CreateCategoryUseCase;
use App\Application\UseCases\Master\DeleteCategoryUseCase;
use App\Application\UseCases\Master\ListCategoriesUseCase;
use App\Application\UseCases\Master\ShowCategoryUseCase;
use App\Application\UseCases\Master\UpdateCategoryUseCase;
use App\Domain\Master\Entities\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateCategoryRequest;
use App\Http\Requests\Master\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ListCategoriesUseCase $listUseCase,
        private readonly ShowCategoryUseCase $showUseCase,
        private readonly CreateCategoryUseCase $createUseCase,
        private readonly UpdateCategoryUseCase $updateUseCase,
        private readonly DeleteCategoryUseCase $deleteUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->listUseCase->execute();

        $search  = (string) $request->query('search', '');
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));

        if ($search !== '') {
            $lower      = strtolower($search);
            $categories = $categories->filter(
                fn($c) => str_contains(strtolower($c->name), $lower)
                       || str_contains(strtolower($c->title), $lower)
            )->values();
        }

        $total  = $categories->count();
        $items  = $categories->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'data' => $items->map(fn($c) => $this->format($c))->values(),
            'meta' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->showUseCase->execute($id);

        return response()->json(['data' => $this->format($category)]);
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $category = $this->createUseCase->execute(
            CreateCategoryDTO::fromArray($request->validated())
        );

        return response()->json(['data' => $this->format($category)], 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->updateUseCase->execute(
            UpdateCategoryDTO::fromArray($id, $request->validated())
        );

        return response()->json(['data' => $this->format($category)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteUseCase->execute($id);

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    private function format(Category $category): array
    {
        return [
            'id'          => $category->id,
            'name'        => $category->name,
            'title'       => $category->title,
            'description' => $category->description,
            'status'      => $category->status->value,
            'created_at'  => $category->createdAt,
            'updated_at'  => $category->updatedAt,
        ];
    }
}
