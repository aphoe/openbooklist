<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_expected_casts(): void
    {
        $user = User::factory()->create([
            'use_ai_description' => 1,
        ]);

        $this->assertIsBool($user->use_ai_description);
        $this->assertTrue($user->use_ai_description);
    }

    public function test_it_has_relationships(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->categories->contains($category));
    }
}
