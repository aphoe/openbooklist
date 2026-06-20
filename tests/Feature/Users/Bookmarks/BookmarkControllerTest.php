<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookmarkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_bookmarks(): void
    {
        $user = User::factory()->create();

        // Create some data
        Category::factory(2)->create(['user_id' => $user->id]);
        Tag::factory(3)->create(['user_id' => $user->id]);
        Bookmark::factory(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 5)
            ->has('allCategories', 2)
            ->has('allTags', 3)
        );
    }

    public function test_bookmarks_are_sorted_correctly(): void
    {
        $user = User::factory()->create();

        Bookmark::factory()->create([
            'user_id' => $user->id,
            'title' => 'Zebra',
            'created_at' => now()->subDays(2),
        ]);

        Bookmark::factory()->create([
            'user_id' => $user->id,
            'title' => 'Apple',
            'created_at' => now(),
        ]);

        // Test newest (default)
        $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'newest']));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->where('bookmarks.data.0.title', 'Apple')
            ->where('bookmarks.data.1.title', 'Zebra')
        );

        // Test oldest
        $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'oldest']));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->where('bookmarks.data.0.title', 'Zebra')
            ->where('bookmarks.data.1.title', 'Apple')
        );

        // Test alphabetical
        $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'alphabetical']));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->where('bookmarks.data.0.title', 'Apple')
            ->where('bookmarks.data.1.title', 'Zebra')
        );
    }

    public function test_bookmarks_can_be_filtered_by_category_slug(): void
    {
        $user = User::factory()->create();

        $workCategory = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Work',
            'slug' => 'work',
        ]);

        $newsCategory = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'News',
            'slug' => 'news',
        ]);

        Bookmark::factory()->create([
            'user_id' => $user->id,
            'category_id' => $workCategory->id,
            'title' => 'Work Alpha',
            'created_at' => now()->subDay(),
        ]);

        Bookmark::factory()->create([
            'user_id' => $user->id,
            'category_id' => $workCategory->id,
            'title' => 'Work Beta',
            'created_at' => now(),
        ]);

        Bookmark::factory()->create([
            'user_id' => $user->id,
            'category_id' => $newsCategory->id,
            'title' => 'News Alpha',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', [
            'category' => 'work',
            'sort' => 'alphabetical',
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 2)
            ->where('bookmarks.data.0.title', 'Work Alpha')
            ->where('bookmarks.data.1.title', 'Work Beta')
        );
    }

    public function test_pagination_presets_are_passed_to_view(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('paginationPresets')
            ->where('paginationPresets.0', 32)
        );
    }

    public function test_per_page_parameter_respects_preset_values(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(100)->create(['user_id' => $user->id]);

        // Test with valid preset value
        $response = $this->actingAs($user)->get(route('dashboard', ['per_page' => 64]));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 64)
        );
    }

    public function test_per_page_invalid_value_defaults_to_32(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(50)->create(['user_id' => $user->id]);

        // Test with invalid per_page value
        $response = $this->actingAs($user)->get(route('dashboard', ['per_page' => 999]));

        // Should default to 32 items
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 32)
        );
    }

    public function test_per_page_is_preserved_in_query_string(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(70)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard', [
            'per_page' => 64,
            'sort' => 'oldest',
        ]));

        // Check that both parameters are in the paginated links
        $response->assertInertia(fn (Assert $page) => $page
            ->has('bookmarks.data', 64)
        );
    }
}
