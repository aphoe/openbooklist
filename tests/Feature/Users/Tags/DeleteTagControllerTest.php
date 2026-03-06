<?php

namespace Tests\Feature\Users\Tags;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('tags.destroy', $tag));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('info', 'Tag deleted successfully.');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_delete_others_tag(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $tag = Tag::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('tags.destroy', $tag));

        $response->assertStatus(403);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
        ]);
    }

    public function test_guests_cannot_delete_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('tags.destroy', $tag));

        $response->assertRedirect(route('login'));
    }
}
