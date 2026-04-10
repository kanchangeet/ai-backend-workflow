<?php

use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok']));

// Module routes
require base_path('Modules/Auth/Routes/api.php');
require base_path('Modules/Master/Routes/api.php');
