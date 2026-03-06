<?php

namespace Tests\Feature\Users\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_categories_index(): void
    {
        $user = User::factory()->create();

        $userCategory1 = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'A user category',
        ]);
        $userCategory2 = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Z user category',
        ]);

        // Another user's category
        $otherCategory = Category::factory()->create([
            'name' => 'Other category',
        ]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('categories.data', 2)
            ->where('categories.data.0.id', $userCategory1->id)
            ->where('categories.data.1.id', $userCategory2->id)
        );
    }

    public function test_guests_cannot_view_categories_index(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }
}
