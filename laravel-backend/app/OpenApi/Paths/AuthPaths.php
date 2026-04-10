<?php

namespace App\OpenApi\Paths;

/**
 * @OA\Post(
 *     path="/auth/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","password_confirmation"},
 *             @OA\Property(property="name",                  type="string",  example="Jane Doe"),
 *             @OA\Property(property="email",                 type="string",  example="jane@example.com"),
 *             @OA\Property(property="password",              type="string",  example="secret123"),
 *             @OA\Property(property="password_confirmation", type="string",  example="secret123")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Created",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="data", ref="#/components/schemas/Category")
 *         )
 *     ),
 *     @OA\Response(response=422, ref="#/components/schemas/ValidationError")
 * )
 *
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Auth"},
 *     summary="Login and obtain a Sanctum token",
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email",    type="string", example="jane@example.com"),
 *             @OA\Property(property="password", type="string", example="secret123")
 *         )
 *     ),
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="data",  type="object")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Invalid credentials")
 * )
 *
 * @OA\Post(
 *     path="/auth/logout",
 *     tags={"Auth"},
 *     summary="Revoke current token",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Logged out")
 * )
 */
class AuthPaths {}
