<?php

namespace Tests\Feature\Users\Tags;

use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_tags_index(): void
    {
        $user = User::factory()->create();

        $tag1 = Tag::factory()->create(['user_id' => $user->id, 'name' => 'Apple']);
        $tag2 = Tag::factory()->create(['user_id' => $user->id, 'name' => 'Banana']);

        $otherUser = User::factory()->create();
        $otherTag = Tag::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other']);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('tags.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Tags/Index')
            ->has('tags.data', 2)
            ->where('tags.data.0.id', $tag1->id)
            ->where('tags.data.1.id', $tag2->id)
        );
    }

    public function test_it_filters_tags_by_search(): void
    {
        $user = User::factory()->create();

        $tag1 = Tag::factory()->create(['user_id' => $user->id, 'name' => 'Apple']);
        $tag2 = Tag::factory()->create(['user_id' => $user->id, 'name' => 'Banana']);

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('tags.index', ['search' => 'App']));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Tags/Index')
            ->has('tags.data', 1)
            ->where('tags.data.0.id', $tag1->id)
        );
    }

    public function test_it_sorts_tags(): void
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['user_id' => $user->id, 'name' => 'Apple', 'created_at' => now()->subDay()]);
        $tag2 = Tag::factory()->create(['user_id' => $user->id, 'name' => 'Banana', 'created_at' => now()]);

        // Give tag2 more bookmarks
        Bookmark::factory()->create(['user_id' => $user->id])->tags()->attach($tag2);
        Bookmark::factory()->create(['user_id' => $user->id])->tags()->attach($tag2);
        Bookmark::factory()->create(['user_id' => $user->id])->tags()->attach($tag1);

        $this->withoutVite();

        // Popularity sort
        $response = $this->actingAs($user)->get(route('tags.index', ['sort' => 'popularity']));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('tags.data.0.id', $tag2->id) // 2 bookmarks
            ->where('tags.data.1.id', $tag1->id) // 1 bookmark
        );

        // Recent sort
        $response = $this->actingAs($user)->get(route('tags.index', ['sort' => 'recent']));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('tags.data.0.id', $tag2->id) // newer
            ->where('tags.data.1.id', $tag1->id) // older
        );
    }

    public function test_guests_cannot_view_tags_index(): void
    {
        $response = $this->get(route('tags.index'));

        $response->assertRedirect(route('login'));
    }
}
