<?php

namespace Tests\Feature\Repositories;

use App\Models\Tag;
use App\Models\User;
use App\Repositories\TagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_tag(): void
    {
        $user = User::factory()->create();

        $repo = new TagRepository;
        $tag = $repo->create(
            user: $user,
            name: 'New Tag',
            description: 'Tag Desc'
        );

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'user_id' => $user->id,
            'name' => 'New Tag',
            'description' => 'Tag Desc',
        ]);

        $this->assertNotNull($tag->slug);
    }

    public function test_it_updates_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $repo = new TagRepository;
        $repo->update(
            tag: $tag,
            user: $user,
            name: 'Updated Name',
            description: 'Updated Desc'
        );

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Name',
            'description' => 'Updated Desc',
        ]);
    }

    public function test_it_deletes_a_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $repo = new TagRepository;
        $deleted = $repo->delete($tag);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
