<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateBookmarkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_bookmark(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id, 'title' => 'Old Title']);
        $newCategory = Category::factory()->create(['user_id' => $user->id]);
        $newTag = Tag::factory()->create(['user_id' => $user->id]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('downloadAndResizeImage')->andReturn('path/to/new_image.jpg');
        });

        $response = $this->actingAs($user)->put(route('bookmarks.update', $bookmark), [
            'url' => 'https://example.com/updated',
            'title' => 'New Title',
            'description' => 'New Description',
            'category_id' => $newCategory->id,
            'tags' => [$newTag->id],
            'image' => 'https://example.com/new.png',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Bookmark updated successfully.');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'title' => 'New Title',
            'description' => 'New Description',
            'category_id' => $newCategory->id,
        ]);

        $this->assertDatabaseHas('bookmark_tag', [
            'bookmark_id' => $bookmark->id,
            'tag_id' => $newTag->id,
        ]);
    }

    public function test_user_cannot_update_another_users_bookmark(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user1->id, 'title' => 'User1 Bookmark']);

        $response = $this->actingAs($user2)->put(route('bookmarks.update', $bookmark), [
            'title' => 'Malicious Title',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'title' => 'User1 Bookmark',
        ]);
    }
}
