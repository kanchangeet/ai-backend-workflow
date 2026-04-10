<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="BFF API",
 *     version="1.0.0",
 *     description="Backend for Frontend — all frontend calls go through here. Login to get a token, then click Authorize.",
 *     @OA\Contact(email="dev@yourdomain.com")
 * )
 *
 * @OA\Server(url="/api", description="BFF (http://localhost:9080)")
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Paste the token from POST /api/auth/login here"
 * )
 *
 * @OA\Schema(
 *     schema="UserData",
 *     @OA\Property(property="id",    type="integer", example=1),
 *     @OA\Property(property="name",  type="string",  example="Jane Doe"),
 *     @OA\Property(property="email", type="string",  example="jane@example.com")
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     @OA\Property(property="id",          type="integer", example=1),
 *     @OA\Property(property="name",        type="string",  example="electronics"),
 *     @OA\Property(property="title",       type="string",  example="Electronics"),
 *     @OA\Property(property="description", type="string",  nullable=true),
 *     @OA\Property(property="status",      type="string",  enum={"active","inactive"}),
 *     @OA\Property(property="created_at",  type="string",  format="date-time"),
 *     @OA\Property(property="updated_at",  type="string",  format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     @OA\Property(property="message", type="string", example="Validation failed."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
 *     )
 * )
 */
class ApiInfo {}
