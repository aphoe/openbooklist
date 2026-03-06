<?php

namespace Tests\Feature\Api\V1\Ext;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_fetch_categories(): void
    {
        $response = $this->getJson('/api/v1/ext/categories');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_fetch_categories_ordered_by_name(): void
    {
        $user = User::factory()->create();

        Category::factory()->create(['name' => 'Zebra Category', 'slug' => 'zebra-category']);
        Category::factory()->create(['name' => 'Apple Category', 'slug' => 'apple-category']);
        Category::factory()->create(['name' => 'Mango Category', 'slug' => 'mango-category']);

        $response = $this->actingAs($user)->getJson('/api/v1/ext/categories');

        $response->assertOk();

        // The exact json order reflects the orderBy('name') in the controller
        $response->assertExactJson([
            'apple-category' => 'Apple Category',
            'mango-category' => 'Mango Category',
            'zebra-category' => 'Zebra Category',
        ]);
    }
}
