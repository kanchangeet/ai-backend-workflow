<?php

namespace App\Services;

use App\Clients\BackendClient;
use App\DTOs\DashboardDTO;
use App\Transformers\DashboardTransformer;

class DashboardService
{
    public function __construct(
        private readonly BackendClient        $client,
        private readonly DashboardTransformer $transformer,
    ) {}

    /**
     * Fetch user and master data concurrently, then aggregate.
     */
    public function aggregate(string $token): DashboardDTO
    {
        $responses = $this->client->concurrentGet([
            'user'        => '/api/auth/me',
            'master'      => '/api/master/categories',
            'users_count' => '/api/auth/users/count',
        ], $token);

        $totalUsers = $responses['users_count']['total'] ?? 0;

        return $this->transformer->transform($responses['user'], $responses['master'], $totalUsers);
    }
}
