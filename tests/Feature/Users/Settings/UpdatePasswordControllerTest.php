<?php

namespace Tests\Feature\Users\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Password updated successfully.');

        $user->refresh();
        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }

    public function test_it_fails_if_current_password_is_incorrect(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_it_fails_if_password_confirmation_does_not_match(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'mismatch-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_guests_cannot_update_password(): void
    {
        $response = $this->put(route('settings.password'), [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect(route('login'));
    }
}
