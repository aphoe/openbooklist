<?php

namespace Tests\Feature\Repositories;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\BookmarkRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_bookmark(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $repo = new BookmarkRepository;
        $bookmark = $repo->create(
            user: $user,
            url: 'https://example.com',
            category: $category,
            title: 'Example',
            description: 'Desc',
            image: 'image.jpg'
        );

        $this->assertInstanceOf(Bookmark::class, $bookmark);
        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'url' => 'https://example.com',
            'title' => 'Example',
            'description' => 'Desc',
            'image' => 'image.jpg',
        ]);
    }

    public function test_it_updates_a_bookmark(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $repo = new BookmarkRepository;
        $repo->update(
            bookmark: $bookmark,
            user: $user,
            url: 'https://updated.com',
            category: $category,
            title: 'Updated',
            description: 'New Desc',
            image: 'new.jpg'
        );

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'category_id' => $category->id,
            'url' => 'https://updated.com',
            'title' => 'Updated',
            'description' => 'New Desc',
            'image' => 'new.jpg',
        ]);
    }

    public function test_it_syncs_tags(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);
        $tag1 = Tag::factory()->create(['user_id' => $user->id]);
        $tag2 = Tag::factory()->create(['user_id' => $user->id]);

        $repo = new BookmarkRepository;
        $repo->syncTags($bookmark, [$tag1->id, $tag2->id]);

        $this->assertCount(2, $bookmark->tags);
        $this->assertTrue($bookmark->tags->contains($tag1));
        $this->assertTrue($bookmark->tags->contains($tag2));
    }

    public function test_it_updates_favorite_status(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id, 'favorite' => false]);

        $repo = new BookmarkRepository;
        $repo->updateFavoriteStatus($bookmark, true);

        $this->assertTrue((bool) $bookmark->fresh()->favorite);
    }

    public function test_it_deletes_a_bookmark(): void
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $repo = new BookmarkRepository;
        $deleted = $repo->delete($bookmark);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('bookmarks', ['id' => $bookmark->id]);
    }
}
