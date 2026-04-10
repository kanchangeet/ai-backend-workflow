<?php

namespace App\Http\Controllers;

use App\DTOs\MasterItemDTO;
use App\Services\MasterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function __construct(
        private readonly MasterService $masterService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $token  = $request->attributes->get('bearer_token');
        $params = array_filter($request->only(['page', 'per_page', 'search', 'status']), fn($v) => $v !== null && $v !== '');
        $result = $this->masterService->list($token, $params);

        if (isset($result['data']) && is_array($result['data'])) {
            $result['data'] = array_values(array_map(
                fn($item) => MasterItemDTO::fromBackend($item)->toArray(),
                $result['data']
            ));
        }

        return response()->json($result);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $token  = $request->attributes->get('bearer_token');
        $result = $this->masterService->get($token, $id);

        if (isset($result['data']) && is_array($result['data'])) {
            $result['data'] = MasterItemDTO::fromBackend($result['data'])->toArray();
        }

        return response()->json($result);
    }

    public function store(Request $request): JsonResponse
    {
        $token  = $request->attributes->get('bearer_token');
        $result = $this->masterService->create($token, $request->all());

        if (isset($result['data']) && is_array($result['data'])) {
            $result['data'] = MasterItemDTO::fromBackend($result['data'])->toArray();
        }

        return response()->json($result, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $token  = $request->attributes->get('bearer_token');
        $result = $this->masterService->update($token, $id, $request->all());

        if (isset($result['data']) && is_array($result['data'])) {
            $result['data'] = MasterItemDTO::fromBackend($result['data'])->toArray();
        }

        return response()->json($result);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $token  = $request->attributes->get('bearer_token');
        $result = $this->masterService->delete($token, $id);

        return response()->json($result);
    }

    public function categories(Request $request): JsonResponse
    {
        $token = $request->attributes->get('bearer_token');

        return response()->json(
            $this->masterService->categories($token)
        );
    }
}
