<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->apiResponse($e);
        }

        return parent::render($request, $e);
    }

    private function apiResponse(Throwable $e): \Illuminate\Http\JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }

        if ($e instanceof BackendException) {
            return response()->json(['message' => $e->getMessage()], $e->status);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'message' => app()->isProduction() ? 'Server error.' : $e->getMessage(),
        ], 500);
    }
}
