<?php

namespace Tests\Feature\Users\Tags;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'description' => 'Old Description',
        ]);

        $response = $this->actingAs($user)->put(route('tags.update', $tag), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Tag updated successfully.');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'user_id' => $user->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }

    public function test_user_cannot_update_others_tag(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $tag = Tag::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->put(route('tags.update', $tag), [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_guests_cannot_update_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $response = $this->put(route('tags.update', $tag), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('login'));
    }
}
