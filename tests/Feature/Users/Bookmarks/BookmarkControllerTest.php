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
}
