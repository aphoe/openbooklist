<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_settings(): void
    {
        $response = $this->get(route('settings.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('settings.general'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $user->refresh();
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('john@gmail.com', $user->email);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_user_can_enable_ai_if_key_is_configured(): void
    {
        Config::set('project.openrouter_key', 'test-key');

        $user = User::factory()->create(['use_ai_description' => false]);

        $response = $this->actingAs($user)->put(route('settings.ai'), [
            'use_ai_description' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $user->refresh();
        $this->assertTrue($user->use_ai_description);
    }

    public function test_user_cannot_enable_ai_if_key_is_missing(): void
    {
        Config::set('project.openrouter_key', null);

        $user = User::factory()->create(['use_ai_description' => false]);

        $response = $this->actingAs($user)->put(route('settings.ai'), [
            'use_ai_description' => true,
        ]);

        $response->assertForbidden();

        $user->refresh();
        $this->assertFalse($user->use_ai_description);
    }

    public function test_user_can_create_access_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.tokens.store'), [
            'name' => 'Test Token',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('newToken');

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'Test Token',
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_user_can_revoke_access_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Revokable Token');

        $response = $this->actingAs($user)->delete(route('settings.tokens.destroy', $token->accessToken->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_user_cannot_revoke_another_users_token(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $otherUser->createToken('Other User Token');

        $response = $this->actingAs($user)->delete(route('settings.tokens.destroy', $token->accessToken->id));

        $response->assertForbidden();

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }
}
