<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    /**
     * GET /dashboard
     *
     * Aggregates user + master data from the backend in a single BFF response.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token     = $request->attributes->get('bearer_token');
        $dashboard = $this->dashboardService->aggregate($token);

        return response()->json($dashboard->toArray());
    }
}
