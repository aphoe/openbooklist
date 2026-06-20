<?php

namespace Tests\Feature\Api\V1\Ext;

use App\Models\Category;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateBookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_bookmark(): void
    {
        $response = $this->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_without_ability_cannot_create_bookmark(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['bookmarks:read']);

        $response = $this->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
        ]);

        $response->assertForbidden();
    }

    public function test_bookmark_creation_requires_url(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['bookmarks:write']);
        $response = $this->postJson('/api/v1/ext/bookmarks', []);

        $response->assertJsonValidationErrors(['url']);
    }

    public function test_category_must_exist(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['bookmarks:write']);
        $response = $this->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
            'category' => 'non-existent-category',
        ]);

        $response->assertJsonValidationErrors(['category']);
    }

    public function test_successful_creation_with_new_tags(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Tech', 'slug' => 'tech']);

        $this->mock(BookmarkService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('fetchMetadata')
                ->once()
                ->with('https://example.com', $user)
                ->andReturn([
                    'title' => 'Example Site',
                    'description' => 'This is an example',
                    'image' => 'https://example.com/image.png',
                ]);

            $mock->shouldReceive('downloadAndResizeImage')
                ->once()
                ->with('https://example.com/image.png')
                ->andReturn('bookmarks/fake-image.png');
        });

        Sanctum::actingAs($user, ['bookmarks:write']);
        $response = $this->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
            'category' => 'tech',
            'tags' => ['laravel', 'php'],
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'title' => 'Example Site',
            'description' => 'This is an example',
            'image' => 'bookmarks/fake-image.png',
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'slug' => 'laravel',
            'name' => 'Laravel',
        ]);

        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'slug' => 'php',
            'name' => 'Php',
        ]);

        $bookmarkId = $response->json('data.id');
        $this->assertDatabaseCount('bookmark_tag', 2);
    }

    public function test_cannot_create_duplicate_url_for_same_user(): void
    {
        $user = User::factory()->create();

        // Create first bookmark
        \App\Models\Bookmark::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        Sanctum::actingAs($user, ['bookmarks:write']);
        $response = $this->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
        ]);

        $response->assertConflict();
        $response->assertJson([
            'message' => 'A bookmark with this URL already exists.',
        ]);
        $this->assertDatabaseCount('bookmarks', 1);
    }

    public function test_different_users_can_create_bookmarks_with_same_url(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // User 1 creates a bookmark
        \App\Models\Bookmark::factory()->create([
            'user_id' => $user1->id,
            'url' => 'https://example.com',
        ]);

        // User 2 tries to create the same URL
        Sanctum::actingAs($user2, ['bookmarks:write']);

        // We need to mock the service for user 2's request
        $this->app->make('Illuminate\Container\Container')
            ->bind(BookmarkService::class, function () {
                $mock = \Mockery::mock(BookmarkService::class);
                $mock->shouldReceive('fetchMetadata')->andReturn([
                    'title' => 'Example',
                    'description' => 'Test',
                    'image' => null,
                ]);
                return $mock;
            });

        $response = $this->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
        ]);

        // Should succeed for different user
        $response->assertCreated();
        $this->assertDatabaseCount('bookmarks', 2);
    }
}
