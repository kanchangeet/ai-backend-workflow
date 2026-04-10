<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterController;
use App\Middleware\ForwardAuthToken;
use Illuminate\Support\Facades\Route;

// Health check (no auth)
Route::get('/health', fn() => response()->json(['status' => 'ok']));

// ── Auth (public) ─────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware(ForwardAuthToken::class)->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// ── Protected routes (token forwarded to backend) ────────────────────────────
Route::middleware(ForwardAuthToken::class)->group(function () {
    Route::get('dashboard', DashboardController::class);

    Route::prefix('master')->group(function () {
        // Named sub-resource must come before wildcard {id}
        Route::get('categories', [MasterController::class, 'categories']);

        // Full CRUD — maps to backend /api/master/categories with code↔title mapping
        Route::get('/',       [MasterController::class, 'index']);
        Route::post('/',      [MasterController::class, 'store']);
        Route::get('{id}',    [MasterController::class, 'show']);
        Route::put('{id}',    [MasterController::class, 'update']);
        Route::delete('{id}', [MasterController::class, 'destroy']);
    });
});
