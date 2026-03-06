<?php

namespace Tests\Feature\Users;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RecentlySavedControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('recently-saved'));

        $response->assertRedirect(route('login'));
    }

    public function test_recently_saved_returns_correct_data(): void
    {
        $user = User::factory()->create();

        Bookmark::factory(15)->create(['user_id' => $user->id]);
        Category::factory(12)->create(['user_id' => $user->id]);
        Tag::factory(12)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('recently-saved'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/RecentlySaved/Index')
            ->has('bookmarks', 12)
            ->has('categories', 10)
            ->has('tags', 10)
        );
    }
}
