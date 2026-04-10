<?php

namespace App\OpenApi\Paths;

/**
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Auth"},
 *     summary="Login — get a JWT token",
 *     description="Use the returned token in the Authorize button (top right) to access protected endpoints.",
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email",    type="string", example="jane@example.com"),
 *             @OA\Property(property="password", type="string", example="secret123")
 *         )
 *     ),
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *             @OA\Property(property="data",  ref="#/components/schemas/UserData")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Invalid credentials"),
 *     @OA\Response(response=422, ref="#/components/schemas/ValidationError")
 * )
 *
 * @OA\Post(
 *     path="/auth/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","password_confirmation"},
 *             @OA\Property(property="name",                  type="string", example="Jane Doe"),
 *             @OA\Property(property="email",                 type="string", example="jane@example.com"),
 *             @OA\Property(property="password",              type="string", example="secret123"),
 *             @OA\Property(property="password_confirmation", type="string", example="secret123")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Created",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="data",  ref="#/components/schemas/UserData")
 *         )
 *     ),
 *     @OA\Response(response=422, ref="#/components/schemas/ValidationError")
 * )
 *
 * @OA\Post(
 *     path="/auth/logout",
 *     tags={"Auth"},
 *     summary="Logout — revoke current token",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Logged out",
 *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Logged out successfully."))
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class AuthPaths {}
