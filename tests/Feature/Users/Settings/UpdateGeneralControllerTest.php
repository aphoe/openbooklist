<?php

namespace Tests\Feature\Users\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateGeneralControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->put(route('settings.general'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'language' => 'es',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Profile information updated successfully.');

        $user->refresh();
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('john@gmail.com', $user->email);
        $this->assertEquals('es', $user->language);
    }

    public function test_it_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('settings.general'), [
            'first_name' => '',
            'last_name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email']);
    }

    public function test_it_validates_language_against_available_options(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('settings.general'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'language' => 'xx-yy',
        ]);

        $response->assertSessionHasErrors(['language']);
    }

    public function test_guests_cannot_update_general_settings(): void
    {
        $response = $this->put(route('settings.general'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect(route('login'));
    }
}
