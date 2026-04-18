<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SetBookmarkImageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_bookmark_image_from_url(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'image' => 'bookmarks/old.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('downloadAndResizeImage')
                ->once()
                ->with('https://example.com/new-image.jpg')
                ->andReturn('bookmarks/new-image.jpg');
            $mock->shouldReceive('deleteStoredImage')
                ->once()
                ->with('bookmarks/old.jpg');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'url',
            'image_url' => 'https://example.com/new-image.jpg',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Bookmark image updated successfully.');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'image' => 'bookmarks/new-image.jpg',
        ]);
    }

    public function test_user_cannot_set_image_for_another_users_bookmark(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'url',
            'image_url' => 'https://example.com/new-image.jpg',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_set_bookmark_image_using_website_screenshot(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com/article',
            'image' => 'bookmarks/old.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('takeWebsiteScreenshot')
                ->once()
                ->with('https://example.com/article')
                ->andReturn('bookmarks/screenshot.jpg');
            $mock->shouldReceive('deleteStoredImage')
                ->once()
                ->with('bookmarks/old.jpg');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'screenshot',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Bookmark image updated successfully.');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'image' => 'bookmarks/screenshot.jpg',
        ]);
    }

    public function test_set_image_requires_a_valid_url_when_url_source_is_selected(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'url',
            'image_url' => 'not-a-valid-url',
        ]);

        $response->assertSessionHasErrors(['image_url']);
    }

    public function test_set_image_does_not_require_image_url_when_screenshot_source_is_selected(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com/article',
            'image' => null,
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('takeWebsiteScreenshot')
                ->once()
                ->with('https://example.com/article')
                ->andReturn('bookmarks/screenshot.jpg');
            $mock->shouldNotReceive('deleteStoredImage');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'screenshot',
        ]);

        $response->assertSessionHasNoErrors(['image_url']);
    }

    public function test_it_shows_error_when_image_download_fails(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'image' => 'bookmarks/original.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('downloadAndResizeImage')
                ->once()
                ->with('https://example.com/new-image.jpg')
                ->andReturn(null);
            $mock->shouldNotReceive('deleteStoredImage');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'url',
            'image_url' => 'https://example.com/new-image.jpg',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Failed to download image from the provided URL.');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'image' => 'bookmarks/original.jpg',
        ]);
    }

    public function test_it_shows_error_when_website_screenshot_capture_fails(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com/article',
            'image' => 'bookmarks/original.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('takeWebsiteScreenshot')
                ->once()
                ->with('https://example.com/article')
                ->andReturn(null);
            $mock->shouldNotReceive('deleteStoredImage');
        });

        $response = $this->actingAs($user)->post(route('bookmarks.set-image', $bookmark), [
            'image_source' => 'screenshot',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Failed to capture website screenshot for this bookmark.');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'image' => 'bookmarks/original.jpg',
        ]);
    }
}
