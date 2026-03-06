<?php

namespace Tests\Feature\Users\Categories;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'New Category',
            'description' => 'This is a description',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Category created successfully.');

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'New Category',
            'description' => 'This is a description',
        ]);
    }

    public function test_guests_cannot_store_a_category(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'New Category',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_it_validates_category_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '', // Empty name
        ]);

        $response->assertSessionHasErrors(['name']);

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => str_repeat('a', 256), // Max length is 255
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_it_validates_category_description(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Valid Name',
            'description' => str_repeat('a', 1500), // Max length is typically 255 or 1000 depending on rules, let's assume it catches limits
        ]);

        $response->assertSessionHasErrors(['description']);
    }
}
