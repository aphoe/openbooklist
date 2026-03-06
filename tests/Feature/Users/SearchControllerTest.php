<?php

namespace Tests\Feature\Users;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_unauthenticated_user_cannot_access_search(): void
    {
        $response = $this->get(route('search'));

        $response->assertRedirect(route('login'));
    }

    public function test_empty_search_returns_empty_results(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('search'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Search/Index')
            ->where('query', '')
            ->has('bookmarks', 0)
            ->has('categories', 0)
            ->has('tags', 0)
            ->where('tab', 'all')
        );
    }

    public function test_search_returns_relevant_results(): void
    {
        $user = User::factory()->create();

        Bookmark::factory()->create([
            'user_id' => $user->id,
            'title' => 'Laravel Tips',
            'description' => 'A great framework',
        ]);
        Bookmark::factory()->create([
            'user_id' => $user->id,
            'title' => 'Vue JS',
            'description' => 'Laravel pairing',
        ]);

        Category::factory()->create(['user_id' => $user->id, 'name' => 'Laravel Packages']);
        Tag::factory()->create(['user_id' => $user->id, 'name' => 'laravel-news']);

        $response = $this->actingAs($user)->get(route('search', ['q' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Search/Index')
            ->where('query', 'Laravel')
            ->has('bookmarks', 2)
            ->has('categories', 1)
            ->has('tags', 1)
            ->where('bookmarks.0.title', 'Laravel Tips')
        );
    }
}
