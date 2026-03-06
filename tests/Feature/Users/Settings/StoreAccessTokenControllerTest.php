<?php

namespace Tests\Feature\Users\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAccessTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_access_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.tokens.store'), [
            'name' => 'My Application Token',
            'abilities' => ['read', 'write'],
            'expires_in' => 30, // 30 days
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('newToken');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'My Application Token',
            'abilities' => json_encode(['read', 'write']),
        ]);

        // Assert expires_at is broadly set (+30 days)
        $this->assertNotNull($user->tokens()->first()->expires_at);
    }

    public function test_it_defaults_to_all_abilities_when_none_provided(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.tokens.store'), [
            'name' => 'Default Abilities Token',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Default Abilities Token',
            'abilities' => json_encode(['*']),
        ]);
    }

    public function test_guests_cannot_create_access_token(): void
    {
        $response = $this->post(route('settings.tokens.store'), [
            'name' => 'Hacked Token',
        ]);

        $response->assertRedirect(route('login'));
    }
}
