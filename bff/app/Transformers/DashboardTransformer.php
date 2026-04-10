<?php

namespace App\Transformers;

use App\DTOs\DashboardDTO;
use App\DTOs\MasterItemDTO;
use App\DTOs\UserDTO;
use Illuminate\Support\Collection;

class DashboardTransformer
{
    /**
     * Build a DashboardDTO from raw backend responses.
     *
     * @param array $userResponse   Raw response from GET /api/auth/me
     * @param array $masterResponse Raw response from GET /api/master/categories
     */
    public function transform(array $userResponse, array $masterResponse, int $totalUsers): DashboardDTO
    {
        $user            = UserDTO::fromArray($userResponse['data']);
        $totalCategories = $masterResponse['meta']['total'] ?? count($masterResponse['data'] ?? []);

        return new DashboardDTO(
            user:            $user,
            totalCategories: $totalCategories,
            totalUsers:      $totalUsers,
        );
    }
}
