<?php

namespace Tests\Feature\Managers;

use App\Managers\ModelManager;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_unique_slug(): void
    {
        $manager = new ModelManager;
        $slug = $manager->generateUniqueSlug('tags', 'A brand new tag');

        $this->assertEquals('a-brand-new-tag', $slug);
    }

    public function test_it_generates_a_unique_slug_with_collisions(): void
    {
        $user = \App\Models\User::factory()->create();
        Tag::factory()->create(['user_id' => $user->id, 'slug' => 'test-slug']);
        Tag::factory()->create(['user_id' => $user->id, 'slug' => 'test-slug-1']);

        $manager = new ModelManager;
        $slug = $manager->generateUniqueSlug('tags', 'Test Slug');

        $this->assertEquals('test-slug-2', $slug);
    }
}
