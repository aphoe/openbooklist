<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_profile(): void
    {
        $user = User::factory()->create();

        $repo = new UserRepository;
        $result = $repo->updateProfile($user, 'Jane', 'Doe', 'jane@example.com');

        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_it_updates_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old_password')]);

        $repo = new UserRepository;
        $result = $repo->updatePassword($user, 'new_password');

        $this->assertTrue($result);
        $this->assertTrue(Hash::check('new_password', $user->fresh()->password));
    }

    public function test_it_updates_ai_description_flag(): void
    {
        $user = User::factory()->create(['use_ai_description' => false]);

        $repo = new UserRepository;
        $result = $repo->updateAiDescription($user, true);

        $this->assertTrue($result);
        $this->assertTrue($user->fresh()->use_ai_description);
    }

    public function test_it_updates_ai_model(): void
    {
        $user = User::factory()->create(['ai_model' => null]);

        $repo = new UserRepository;
        $result = $repo->updateAiModel($user, 'openai/gpt-4o');

        $this->assertTrue($result);
        $this->assertEquals('openai/gpt-4o', $user->fresh()->ai_model);
    }
}
