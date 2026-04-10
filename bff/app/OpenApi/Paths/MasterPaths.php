<?php

namespace App\OpenApi\Paths;

/**
 * @OA\Get(
 *     path="/master/categories",
 *     tags={"Master"},
 *     summary="List all categories",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Category")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=502, description="Backend unavailable")
 * )
 */
class MasterPaths {}
