<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    private string $token = 'test-bearer-token';

    public function test_dashboard_returns_aggregated_data(): void
    {
        Http::fake([
            '*/api/auth/me'              => Http::response([
                'data' => ['id' => 1, 'name' => 'Jane', 'email' => 'jane@example.com'],
            ], 200),
            '*/api/master/categories'    => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'electronics', 'title' => 'Electronics', 'description' => null, 'status' => 'active'],
                ],
            ], 200),
        ]);

        $this->withToken($this->token)
            ->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonStructure(['user', 'master'])
            ->assertJsonPath('user.name', 'Jane')
            ->assertJsonCount(1, 'master');
    }

    public function test_dashboard_requires_bearer_token(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }

    public function test_dashboard_propagates_backend_502_on_failure(): void
    {
        Http::fake([
            '*/api/auth/me'           => Http::response([], 500),
            '*/api/master/categories' => Http::response([], 200),
        ]);

        $this->withToken($this->token)
            ->getJson('/api/dashboard')
            ->assertStatus(502);
    }

    public function test_health_check_returns_ok(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }
}
