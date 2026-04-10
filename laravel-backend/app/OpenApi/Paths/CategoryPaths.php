<?php

namespace App\OpenApi\Paths;

/**
 * @OA\Get(
 *     path="/master/categories",
 *     tags={"Master - Categories"},
 *     summary="List all categories",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/master/categories",
 *     tags={"Master - Categories"},
 *     summary="Create a category",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(
 *             required={"name","title"},
 *             @OA\Property(property="name",        type="string", example="electronics"),
 *             @OA\Property(property="title",       type="string", example="Electronics"),
 *             @OA\Property(property="description", type="string", nullable=true),
 *             @OA\Property(property="status",      type="string", enum={"active","inactive"}, default="active")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Created",
 *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Category"))
 *     ),
 *     @OA\Response(response=422, ref="#/components/schemas/ValidationError")
 * )
 *
 * @OA\Get(
 *     path="/master/categories/{id}",
 *     tags={"Master - Categories"},
 *     summary="Get a single category",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Category"))
 *     ),
 *     @OA\Response(response=422, description="Not found")
 * )
 *
 * @OA\Put(
 *     path="/master/categories/{id}",
 *     tags={"Master - Categories"},
 *     summary="Update a category",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true,
 *         @OA\JsonContent(
 *             required={"name","title","status"},
 *             @OA\Property(property="name",        type="string"),
 *             @OA\Property(property="title",       type="string"),
 *             @OA\Property(property="description", type="string", nullable=true),
 *             @OA\Property(property="status",      type="string", enum={"active","inactive"})
 *         )
 *     ),
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Category"))
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/master/categories/{id}",
 *     tags={"Master - Categories"},
 *     summary="Delete a category",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Deleted")
 * )
 */
class CategoryPaths {}
