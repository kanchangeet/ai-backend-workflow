<?php

namespace Tests\Unit\Domain\Master;

use App\Domain\Master\Entities\Category;
use App\Domain\Master\Enums\MasterStatus;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function test_create_returns_active_category_with_null_id(): void
    {
        $category = Category::create(
            name: 'electronics',
            title: 'Electronics',
            description: 'Electronic goods',
        );

        $this->assertNull($category->id);
        $this->assertSame('electronics', $category->name);
        $this->assertSame('Electronics', $category->title);
        $this->assertSame(MasterStatus::Active, $category->status);
    }

    public function test_create_accepts_inactive_status(): void
    {
        $category = Category::create(
            name: 'archived',
            title: 'Archived',
            status: MasterStatus::Inactive,
        );

        $this->assertSame(MasterStatus::Inactive, $category->status);
    }

    public function test_update_returns_new_instance_with_same_id(): void
    {
        $original = new Category(
            id: 42,
            name: 'old-name',
            title: 'Old Title',
            description: null,
            status: MasterStatus::Active,
            createdAt: '2024-01-01T00:00:00+00:00',
            updatedAt: null,
        );

        $updated = $original->update(
            name: 'new-name',
            title: 'New Title',
            description: 'Updated desc',
            status: MasterStatus::Inactive,
        );

        $this->assertSame(42, $updated->id);
        $this->assertSame('new-name', $updated->name);
        $this->assertSame('New Title', $updated->title);
        $this->assertSame(MasterStatus::Inactive, $updated->status);
        // Original is untouched (immutability)
        $this->assertSame('old-name', $original->name);
    }

    public function test_description_is_nullable(): void
    {
        $category = Category::create(name: 'test', title: 'Test');

        $this->assertNull($category->description);
    }
}
