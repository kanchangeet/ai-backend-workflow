<?php

namespace Tests\Unit\Transformers;

use App\Transformers\DashboardTransformer;
use PHPUnit\Framework\TestCase;

class DashboardTransformerTest extends TestCase
{
    private DashboardTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new DashboardTransformer();
    }

    public function test_transforms_backend_responses_into_dashboard_dto(): void
    {
        $userResponse = [
            'data' => ['id' => 1, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
        ];

        $masterResponse = [
            'data' => [
                ['id' => 1, 'name' => 'electronics', 'title' => 'Electronics', 'description' => null, 'status' => 'active'],
                ['id' => 2, 'name' => 'furniture',   'title' => 'Furniture',   'description' => 'Home', 'status' => 'active'],
            ],
        ];

        $dto    = $this->transformer->transform($userResponse, $masterResponse);
        $result = $dto->toArray();

        $this->assertSame(1, $result['user']['id']);
        $this->assertSame('Jane Doe', $result['user']['name']);
        $this->assertCount(2, $result['master']);
        $this->assertSame('electronics', $result['master'][0]['name']);
    }

    public function test_master_is_empty_when_backend_returns_no_items(): void
    {
        $dto = $this->transformer->transform(
            ['data' => ['id' => 1, 'name' => 'Jane', 'email' => 'jane@example.com']],
            ['data' => []],
        );

        $this->assertCount(0, $dto->toArray()['master']);
    }

    public function test_dashboard_dto_to_array_has_correct_keys(): void
    {
        $dto    = $this->transformer->transform(
            ['data' => ['id' => 5, 'name' => 'Bob', 'email' => 'bob@example.com']],
            ['data' => []],
        );
        $result = $dto->toArray();

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('master', $result);
        $this->assertArrayHasKey('id', $result['user']);
        $this->assertArrayHasKey('name', $result['user']);
        $this->assertArrayHasKey('email', $result['user']);
    }
}
