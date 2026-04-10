<?php

namespace Modules\Auth\Controllers;

use App\Application\DTOs\Auth\RegisterDTO;
use App\Application\UseCases\Auth\RegisterUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterUseCase $registerUseCase,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerUseCase->execute(
            RegisterDTO::fromArray($request->validated())
        );

        $token = auth('api')->login(
            \App\Models\User::findOrFail($result['user']->id)
        );

        return response()->json([
            'data'  => $this->formatUser($result['user']),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = auth('api')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if (! $token) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        return response()->json([
            'data'  => $this->formatUser(auth('api')->user()),
            'token' => $token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->formatUser(auth('api')->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function usersCount(): JsonResponse
    {
        return response()->json(['total' => \App\Models\User::count()]);
    }

    private function formatUser(mixed $user): array
    {
        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ];
    }
}
