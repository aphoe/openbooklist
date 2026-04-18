<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use App\Services\BookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class DeleteBookmarkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_bookmark(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'image' => 'bookmarks/image-to-delete.jpg',
        ]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteStoredImage')
                ->once()
                ->with('bookmarks/image-to-delete.jpg');
        });

        $response = $this->actingAs($user)->delete(route('bookmarks.destroy', $bookmark));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('info', 'Bookmark deleted successfully.');

        $this->assertDatabaseMissing('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_bookmark(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user1->id]);

        $this->mock(BookmarkService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('deleteStoredImage');
        });

        $response = $this->actingAs($user2)->delete(route('bookmarks.destroy', $bookmark));

        $response->assertStatus(403);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }
}
