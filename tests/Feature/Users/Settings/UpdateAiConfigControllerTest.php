<?php

namespace Tests\Feature\Users\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UpdateAiConfigControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_ai_configuration(): void
    {
        // Require openrouter key to allow AI enablement to bypass any disabled-state errors
        Config::set('project.openrouter_key', 'test-key');

        $user = User::factory()->create([
            'use_ai_description' => false,
            'ai_model' => null,
        ]);

        $response = $this->actingAs($user)->put(route('settings.ai'), [
            'use_ai_description' => true,
            'ai_model' => 'openai/gpt-3.5-turbo',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'AI Configuration updated successfully.');

        $user->refresh();
        $this->assertTrue($user->use_ai_description);
        $this->assertEquals('openai/gpt-3.5-turbo', $user->ai_model);
    }

    public function test_guests_cannot_update_ai_configuration(): void
    {
        $response = $this->put(route('settings.ai'), [
            'use_ai_description' => true,
            'ai_model' => 'test-model',
        ]);

        $response->assertRedirect(route('login'));
    }
}
