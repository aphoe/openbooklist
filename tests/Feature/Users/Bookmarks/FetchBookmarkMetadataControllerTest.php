<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class FetchBookmarkMetadataControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_metadata_for_url(): void
    {
        $user = User::factory()->create();

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchMetadata')->with('https://laravel.com')->andReturn([
                'title' => 'Laravel',
                'description' => 'PHP Framework',
                'image' => 'https://laravel.com/img/logomark.min.svg',
            ]);
        });

        $response = $this->actingAs($user)->postJson(route('bookmarks.fetch-metadata'), [
            'url' => 'https://laravel.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'title' => 'Laravel',
                'description' => 'PHP Framework',
                'image' => 'https://laravel.com/img/logomark.min.svg',
            ],
        ]);
    }

    public function test_invalid_url_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('bookmarks.fetch-metadata'), [
            'url' => 'not-a-url',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    public function test_service_failure_returns_graceful_response(): void
    {
        $user = User::factory()->create();

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchMetadata')->andThrow(new \Exception('Connection failed'));
        });

        $response = $this->actingAs($user)->postJson(route('bookmarks.fetch-metadata'), [
            'url' => 'https://example.com',
        ]);

        $response->assertStatus(200); // the controller handles the error and returns 200 with success = false
        $response->assertJson([
            'success' => false,
            'message' => 'Could not fetch metadata for the given URL.',
        ]);
    }
}
