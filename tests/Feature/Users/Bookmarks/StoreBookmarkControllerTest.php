<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class StoreBookmarkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_a_bookmark(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('downloadAndResizeImage')->andReturn('path/to/image.jpg');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.store'), [
            'url' => 'https://laravel.com',
            'title' => 'Laravel Framework',
            'description' => 'The PHP Framework for Web Artisans',
            'category_id' => $category->id,
            'tags' => [$tag->id],
            'image' => 'https://laravel.com/img/logomark.min.svg',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Bookmark saved successfully.');

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'url' => 'https://laravel.com',
            'title' => 'Laravel Framework',
        ]);

        $this->assertDatabaseHas('bookmark_tag', [
            'tag_id' => $tag->id,
        ]);
    }

    public function test_url_is_required_to_store_bookmark(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('bookmarks.store'), [
            'url' => '',
        ]);

        $response->assertSessionHasErrors('url');
    }
}
