<?php

namespace Tests\Feature\Api\V1\Ext;

use App\Models\Category;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_bookmark_creation_requires_url(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/ext/bookmarks', []);

        $response->assertJsonValidationErrors(['url']);
    }

    public function test_category_must_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
            'category' => 'non-existent-category',
        ]);

        $response->assertJsonValidationErrors(['category']);
    }

    public function test_successful_creation_with_new_tags(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Tech', 'slug' => 'tech']);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchMetadata')
                ->once()
                ->with('https://example.com')
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

        $response = $this->actingAs($user)->postJson('/api/v1/ext/bookmarks', [
            'url' => 'https://example.com',
            'category' => 'tech',
            'tag' => ['laravel', 'php'],
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
}
