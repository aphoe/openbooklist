<?php

namespace Tests\Feature\Models;

use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_relationships(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $user->id]);
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $tag->bookmarks()->attach($bookmark);

        $this->assertInstanceOf(User::class, $tag->user);
        $this->assertEquals($user->id, $tag->user->id);

        $this->assertTrue($tag->bookmarks->contains($bookmark));
    }
}
