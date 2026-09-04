<?php

namespace Tests\Feature\Api\V1\App\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Tests\TestCase;

class SetImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_bookmark_image_from_upload(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id, 'image' => null]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('storeUploadedImage')->once()->andReturn('bookmarks/uploaded.jpg');
            $mock->shouldNotReceive('deleteStoredImage');
        });

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/app/bookmarks/{$bookmark->id}/set-image", [
            'image_source' => 'upload',
            'image_file' => UploadedFile::fake()->image('cover.png', 800, 800),
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Bookmark image updated successfully.']);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'image' => 'bookmarks/uploaded.jpg',
        ]);
    }

    public function test_upload_source_requires_a_file(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/app/bookmarks/{$bookmark->id}/set-image", [
            'image_source' => 'upload',
        ]);

        $response->assertJsonValidationErrors(['image_file']);
    }
}
