<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class RefetchBookmarkMetadataControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_refetch_bookmark_metadata(): void
    {
        $user = User::factory()->create(['use_ai_description' => true]);
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com/post',
            'title' => 'Old Title',
            'description' => 'Old Description',
            'image' => 'bookmarks/old.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('fetchMetadata')
                ->once()
                ->with('https://example.com/post', $user)
                ->andReturn([
                    'title' => 'Fetched Title',
                    'description' => 'AI refreshed description',
                    'image' => 'https://example.com/new.png',
                ]);

            $mock->shouldReceive('downloadAndResizeImage')
                ->once()
                ->with('https://example.com/new.png')
                ->andReturn('bookmarks/new.jpg');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.refetch-metadata', $bookmark));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Bookmark metadata refreshed successfully.');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'title' => 'Fetched Title',
            'description' => 'AI refreshed description',
            'image' => 'bookmarks/new.jpg',
        ]);
    }

    public function test_user_cannot_refetch_another_users_bookmark_metadata(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->post(route('bookmarks.refetch-metadata', $bookmark));

        $response->assertForbidden();
    }

    public function test_existing_image_is_kept_when_image_download_fails(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com/post',
            'image' => 'bookmarks/original.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('fetchMetadata')
                ->once()
                ->with('https://example.com/post', $user)
                ->andReturn([
                    'title' => null,
                    'description' => 'New Description',
                    'image' => 'https://example.com/new.png',
                ]);

            $mock->shouldReceive('downloadAndResizeImage')
                ->once()
                ->with('https://example.com/new.png')
                ->andReturn(null);
        });

        $response = $this->actingAs($user)->post(route('bookmarks.refetch-metadata', $bookmark));

        $response->assertRedirect();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'image' => 'bookmarks/original.jpg',
            'description' => 'New Description',
        ]);
    }
}
