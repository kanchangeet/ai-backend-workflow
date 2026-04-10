<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="Laravel Backend API",
 *     version="1.0.0",
 *     description="Production-ready modular Laravel microservice",
 *     @OA\Contact(email="dev@yourdomain.com")
 * )
 *
 * @OA\Server(url="/api", description="API Base URL")
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Sanctum token — obtain via POST /api/auth/login"
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     @OA\Property(property="id",          type="integer", example=1),
 *     @OA\Property(property="name",         type="string",  example="electronics"),
 *     @OA\Property(property="title",        type="string",  example="Electronics"),
 *     @OA\Property(property="description",  type="string",  nullable=true),
 *     @OA\Property(property="status",       type="string",  enum={"active","inactive"}),
 *     @OA\Property(property="created_at",   type="string",  format="date-time"),
 *     @OA\Property(property="updated_at",   type="string",  format="date-time")
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
