<?php

use App\Http\Controllers\Master\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('master')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});
