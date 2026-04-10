<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            email: $request->email,
            password: $request->password,
        );

        return response()->json($this->formatAuthResponse($result));
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            passwordConfirmation: $request->password_confirmation,
        );

        return response()->json($this->formatAuthResponse($result), 201);
    }

    private function formatAuthResponse(array $result): array
    {
        return [
            'token' => $result['token'],
            'user'  => $result['data'],
        ];
    }

    public function logout(Request $request): JsonResponse
    {
        $token  = $request->attributes->get('bearer_token');
        $result = $this->authService->logout($token);

        return response()->json($result);
    }
}
