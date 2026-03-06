<?php

namespace Tests\Feature\Models;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_relationships(): void
    {
        $user = User::factory()->create();
        $parentCategory = Category::factory()->create(['user_id' => $user->id]);
        $childCategory = Category::factory()->create(['user_id' => $user->id, 'parent_id' => $parentCategory->id]);

        $bookmark = Bookmark::factory()->create([
            'user_id' => $user->id,
            'category_id' => $parentCategory->id,
        ]);

        $this->assertInstanceOf(User::class, $parentCategory->user);
        $this->assertEquals($user->id, $parentCategory->user->id);

        $this->assertInstanceOf(Category::class, $childCategory->parent);
        $this->assertEquals($parentCategory->id, $childCategory->parent->id);

        $this->assertTrue($parentCategory->children->contains($childCategory));
        $this->assertTrue($parentCategory->bookmarks->contains($bookmark));
    }
}
