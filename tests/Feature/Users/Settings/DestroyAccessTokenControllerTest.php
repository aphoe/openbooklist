<?php

namespace Tests\Feature\Users\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyAccessTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_revoke_their_own_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token');

        $response = $this->actingAs($user)->delete(route('settings.tokens.destroy', $token->accessToken->id));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_user_cannot_revoke_another_users_token(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $token = $otherUser->createToken('Victim Token');

        $response = $this->actingAs($user)->delete(route('settings.tokens.destroy', $token->accessToken->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_guests_cannot_revoke_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Some Token');

        $response = $this->delete(route('settings.tokens.destroy', $token->accessToken->id));

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }
}
