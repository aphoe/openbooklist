<?php

namespace Tests\Feature\Users\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('info', 'Category deleted successfully.');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_delete_others_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertStatus(403);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_guests_cannot_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('login'));
    }
}
