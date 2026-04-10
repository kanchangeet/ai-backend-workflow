<?php

namespace Tests\Feature\Master;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ── Index ────────────────────────────────────────────────────────────────

    public function test_index_returns_empty_list(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/master/categories')
            ->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonCount(0, 'data');
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/master/categories')->assertUnauthorized();
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function test_store_creates_category(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/master/categories', [
                'name'        => 'electronics',
                'title'       => 'Electronics',
                'description' => 'Consumer electronics',
                'status'      => 'active',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'electronics')
            ->assertJsonPath('data.status', 'active');
    }

    public function test_store_fails_with_duplicate_name(): void
    {
        $payload = ['name' => 'electronics', 'title' => 'Electronics', 'status' => 'active'];

        $this->actingAs($this->user)->postJson('/api/master/categories', $payload)->assertCreated();
        $this->actingAs($this->user)->postJson('/api/master/categories', $payload)->assertUnprocessable();
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/master/categories', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'title']);
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function test_show_returns_category(): void
    {
        $created = $this->actingAs($this->user)
            ->postJson('/api/master/categories', [
                'name'  => 'furniture',
                'title' => 'Furniture',
                'status'=> 'active',
            ])
            ->assertCreated()
            ->json('data');

        $this->actingAs($this->user)
            ->getJson("/api/master/categories/{$created['id']}")
            ->assertOk()
            ->assertJsonPath('data.id', $created['id']);
    }

    public function test_show_returns_404_for_unknown_id(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/master/categories/99999')
            ->assertUnprocessable(); // DomainException → 422 via Handler
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_update_modifies_category(): void
    {
        $created = $this->actingAs($this->user)
            ->postJson('/api/master/categories', ['name' => 'books', 'title' => 'Books', 'status' => 'active'])
            ->json('data');

        $this->actingAs($this->user)
            ->putJson("/api/master/categories/{$created['id']}", [
                'name'   => 'books',
                'title'  => 'Books & Literature',
                'status' => 'inactive',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Books & Literature')
            ->assertJsonPath('data.status', 'inactive');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function test_destroy_deletes_category(): void
    {
        $created = $this->actingAs($this->user)
            ->postJson('/api/master/categories', ['name' => 'temp', 'title' => 'Temp', 'status' => 'active'])
            ->json('data');

        $this->actingAs($this->user)
            ->deleteJson("/api/master/categories/{$created['id']}")
            ->assertOk()
            ->assertJsonPath('message', 'Category deleted successfully.');

        $this->actingAs($this->user)
            ->getJson("/api/master/categories/{$created['id']}")
            ->assertUnprocessable();
    }
}
