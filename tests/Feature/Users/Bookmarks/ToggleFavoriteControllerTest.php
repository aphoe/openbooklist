<?php

namespace Tests\Feature\Users\Bookmarks;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleFavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_favorite_status(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id, 'favorite' => false]);

        $response = $this->actingAs($user)->post(route('bookmarks.favorite', $bookmark));

        $response->assertRedirect();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'favorite' => true,
        ]);

        // Toggle back
        $response = $this->actingAs($user)->post(route('bookmarks.favorite', $bookmark));

        $response->assertRedirect();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'favorite' => false,
        ]);
    }

    public function test_user_cannot_toggle_another_users_bookmark(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user1->id, 'favorite' => false]);

        $response = $this->actingAs($user2)->post(route('bookmarks.favorite', $bookmark));

        $response->assertStatus(403);
    }
}
