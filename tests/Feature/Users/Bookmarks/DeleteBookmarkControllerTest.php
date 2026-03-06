<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBookmarkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_bookmark(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

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

        $response = $this->actingAs($user2)->delete(route('bookmarks.destroy', $bookmark));

        $response->assertStatus(403);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }
}
