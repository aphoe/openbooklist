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

    public function test_pagination_presets_are_passed_to_view(): void
    {
        $user = User::factory()->create();
        Category::factory(5)->create(['user_id' => $user->id]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('paginationPresets')
            ->where('paginationPresets.0', 10)
        );
    }

    public function test_per_page_parameter_respects_preset_values(): void
    {
        $user = User::factory()->create();
        Category::factory(50)->create(['user_id' => $user->id]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index', ['per_page' => 50]));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('categories.data', 50)
        );
    }

    public function test_per_page_invalid_value_defaults_to_25(): void
    {
        $user = User::factory()->create();
        Category::factory(50)->create(['user_id' => $user->id]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index', ['per_page' => 999]));

        // Should default to 25 items
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('categories.data', 25)
        );
    }
}
