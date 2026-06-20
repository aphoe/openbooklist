<?php

namespace Tests\Feature\Users\Categories;

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use App\Services\CategoryService;
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

    public function test_per_page_is_read_from_user_setting(): void
    {
        $user = User::factory()->create();
        Category::factory(50)->create(['user_id' => $user->id]);
        Setting::factory()->create([
            'user_id' => $user->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '50',
        ]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('categories.data', 50)
            ->where('perPage', 50)
        );
    }

    public function test_per_page_defaults_to_25_when_no_setting_exists(): void
    {
        $user = User::factory()->create();
        Category::factory(50)->create(['user_id' => $user->id]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('categories.data', 25)
            ->where('perPage', 25)
        );
    }

    public function test_per_page_prop_is_passed_to_view(): void
    {
        $user = User::factory()->create();
        Category::factory(5)->create(['user_id' => $user->id]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Categories/Index')
            ->has('perPage')
        );
    }

    public function test_per_page_setting_of_other_user_is_not_used(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Category::factory(50)->create(['user_id' => $user->id]);
        Setting::factory()->create([
            'user_id' => $other->id,
            'setting' => CategoryService::PER_PAGE_SETTING,
            'value' => '50',
        ]);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('perPage', 25)
        );
    }
}
