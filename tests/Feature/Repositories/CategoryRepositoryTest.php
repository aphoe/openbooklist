<?php

namespace Tests\Feature\Repositories;

use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_category(): void
    {
        $user = User::factory()->create();
        $parent = Category::factory()->create(['user_id' => $user->id]);

        $repo = new CategoryRepository;
        $category = $repo->create(
            user: $user,
            name: 'New Category',
            parent: $parent,
            description: 'Cat Desc'
        );

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'user_id' => $user->id,
            'parent_id' => $parent->id,
            'name' => 'New Category',
            'description' => 'Cat Desc',
        ]);

        $this->assertNotNull($category->slug);
    }

    public function test_it_updates_a_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $newParent = Category::factory()->create(['user_id' => $user->id]);

        $repo = new CategoryRepository;
        $repo->update(
            category: $category,
            user: $user,
            name: 'Updated Name',
            parent: $newParent,
            description: 'Updated Desc'
        );

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $newParent->id,
            'name' => 'Updated Name',
            'description' => 'Updated Desc',
        ]);
    }

    public function test_it_deletes_a_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $repo = new CategoryRepository;
        $deleted = $repo->delete($category);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
