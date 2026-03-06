<?php

namespace Tests\Feature\Users\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_settings_index_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        // Give the user some tokens to assert they are passed
        $user->createToken('Test Token 1');
        $user->createToken('Test Token 2');

        $this->withoutVite();
        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Settings/Index')
            ->has('tokens', 2)
        );
    }

    public function test_guests_cannot_view_settings_index(): void
    {
        $response = $this->get(route('settings.index'));

        $response->assertRedirect(route('login'));
    }
}
