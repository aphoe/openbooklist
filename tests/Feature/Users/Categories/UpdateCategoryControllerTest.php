<?php

namespace Tests\Feature\Users\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'description' => 'Old Description',
        ]);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Category updated successfully.');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'user_id' => $user->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }

    public function test_user_cannot_update_others_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_guests_cannot_update_a_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->put(route('categories.update', $category), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('login'));
    }
}
