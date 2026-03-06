<?php

namespace Tests\Feature\Models;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accesses_image_url_correctly(): void
    {
        // Null image uses default
        $bookmark = Bookmark::factory()->make(['image' => null]);
        $this->assertEquals(asset('assets/images/defaults/bookmark.jpg'), $bookmark->image_url);

        // Valid URL passes through
        $bookmark = Bookmark::factory()->make(['image' => 'https://example.com/image.jpg']);
        $this->assertEquals('https://example.com/image.jpg', $bookmark->image_url);

        // Path gets prefixed with storage url
        $bookmark = Bookmark::factory()->make(['image' => 'bookmarks/image.jpg']);
        $this->assertEquals(asset('storage/bookmarks/image.jpg'), $bookmark->image_url);
    }

    public function test_it_accesses_domain_correctly(): void
    {
        $bookmark = Bookmark::factory()->make(['url' => null]);
        $this->assertNull($bookmark->domain);

        $bookmark = Bookmark::factory()->make(['url' => 'https://foo.bar.com/baz?123']);
        $this->assertEquals('foo.bar.com', $bookmark->domain);

        $bookmark = Bookmark::factory()->make(['url' => 'http://www.google.com']);
        $this->assertEquals('google.com', $bookmark->domain);
    }

    public function test_it_has_relationships(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $bookmark->tags()->attach($tag);

        $this->assertInstanceOf(User::class, $bookmark->user);
        $this->assertInstanceOf(Category::class, $bookmark->category);
        $this->assertTrue($bookmark->tags->contains($tag));

        $this->assertEquals($user->id, $bookmark->user->id);
        $this->assertEquals($category->id, $bookmark->category->id);
    }
}
