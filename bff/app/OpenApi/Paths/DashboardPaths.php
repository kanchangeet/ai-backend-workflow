<?php

namespace App\OpenApi\Paths;

/**
 * @OA\Get(
 *     path="/dashboard",
 *     tags={"Dashboard"},
 *     summary="Aggregated dashboard — user + categories in one call",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="OK",
 *         @OA\JsonContent(
 *             @OA\Property(property="user",   ref="#/components/schemas/UserData"),
 *             @OA\Property(property="master", type="array", @OA\Items(ref="#/components/schemas/Category"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=502, description="Backend unavailable")
 * )
 */
class DashboardPaths {}
