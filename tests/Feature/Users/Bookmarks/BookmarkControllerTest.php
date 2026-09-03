<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use App\Services\BookmarkService;
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

    public function test_bookmarks_can_be_filtered_by_tag_slug(): void
    {
        $user = User::factory()->create();

        $laravelTag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $vueTag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Vue',
            'slug' => 'vue',
        ]);

        $tagged = Bookmark::factory(2)->create(['user_id' => $user->id]);
        $tagged->each(fn (Bookmark $bookmark) => $bookmark->tags()->attach($laravelTag->id));

        Bookmark::factory()->create(['user_id' => $user->id])->tags()->attach($vueTag->id);
        Bookmark::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard', ['tag' => 'laravel']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 2)
            ->where('activeFilter.type', 'tag')
            ->where('activeFilter.label', 'Laravel')
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
            ->where('paginationPresets.0', 16)
        );
    }

    public function test_per_page_is_read_from_user_setting(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(100)->create(['user_id' => $user->id]);
        Setting::factory()->create([
            'user_id' => $user->id,
            'setting' => BookmarkService::PER_PAGE_SETTING,
            'value' => '64',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 64)
            ->where('perPage', 64)
        );
    }

    public function test_per_page_defaults_to_32_when_no_setting_exists(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(50)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('bookmarks.data', 32)
            ->where('perPage', 32)
        );
    }

    public function test_per_page_prop_is_passed_to_view(): void
    {
        $user = User::factory()->create();
        Bookmark::factory(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Bookmarks/Index')
            ->has('perPage')
        );
    }

    public function test_per_page_setting_of_other_user_is_not_used(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Bookmark::factory(50)->create(['user_id' => $user->id]);
        Setting::factory()->create([
            'user_id' => $other->id,
            'setting' => BookmarkService::PER_PAGE_SETTING,
            'value' => '64',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('perPage', 32)
        );
    }
}
