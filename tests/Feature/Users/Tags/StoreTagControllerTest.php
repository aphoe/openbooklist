<?php

namespace Tests\Feature\Users\Tags;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_tag(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tags.store'), [
            'name' => 'New Tag',
            'description' => 'A valid description',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Tag created successfully.');

        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'name' => 'New Tag',
            'description' => 'A valid description',
        ]);
    }

    public function test_guests_cannot_store_a_tag(): void
    {
        $response = $this->post(route('tags.store'), [
            'name' => 'Hacked Tag',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_it_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tags.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }
}
