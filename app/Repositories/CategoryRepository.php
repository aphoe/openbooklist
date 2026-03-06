<?php

namespace App\Repositories;

use App\Managers\ModelManager;
use App\Models\Category;
use App\Models\User;

final class CategoryRepository
{
    /**
     * Create a new Category record.
     */
    public function create(
        User $user,
        string $name,
        ?Category $parent = null,
        ?string $description = null,
    ): Category {
        $category = new Category;

        $category->user_id = $user->id;
        $category->parent_id = $parent?->id;
        $category->name = $name;
        $category->slug = (new ModelManager)->generateUniqueSlug('categories', $name);
        $category->description = $description;

        $category->save();

        return $category;
    }

    /**
     * Update an existing Category record.
     */
    public function update(
        Category $category,
        User $user,
        string $name,
        ?Category $parent = null,
        ?string $description = null,
    ): Category {
        $category->user_id = $user->id;
        $category->parent_id = $parent?->id;

        $category->name = $name;
        $category->description = $description;

        $category->save();

        return $category;
    }

    /**
     * Delete a Category record.
     */
    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
