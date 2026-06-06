<?php

namespace App\Repositories;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class BookmarkRepository
{
    /**
     * Get the maximum title length from the database schema.
     */
    private static function getTitleMaxLength(): int
    {
        $columns = Schema::getColumns('bookmarks');
        foreach ($columns as $column) {
            if ($column['name'] === 'title' && isset($column['length'])) {
                return $column['length'];
            }
        }

        return 255; // Fallback to Laravel's default string length
    }

    /**
     * Get the maximum description length from the database schema.
     */
    private static function getDescriptionMaxLength(): int
    {
        $columns = Schema::getColumns('bookmarks');
        foreach ($columns as $column) {
            if ($column['name'] === 'description' && isset($column['length'])) {
                return $column['length'];
            }
        }

        return 65535; // Fallback to text field max
    }

    /**
     * Create a new Bookmark record.
     */
    public function create(
        User $user,
        string $url,
        ?Category $category = null,
        ?string $title = null,
        ?string $description = null,
        ?string $image = null
    ): Bookmark {
        $bookmark = new Bookmark;

        $bookmark->user_id = $user->id;
        $bookmark->category_id = $category?->id;
        $bookmark->url = $url;
        $bookmark->title = $title !== null ? Str::limit($title, self::getTitleMaxLength(), '') : null;
        $bookmark->description = $description !== null ? Str::limit($description, self::getDescriptionMaxLength(), '') : null;
        $bookmark->image = $image;

        $bookmark->save();

        return $bookmark;
    }

    /**
     * Update an existing Bookmark record.
     */
    public function update(
        Bookmark $bookmark,
        User $user,
        string $url,
        ?Category $category = null,
        ?string $title = null,
        ?string $description = null,
        ?string $image = null
    ): Bookmark {
        $bookmark->user_id = $user->id;
        $bookmark->category_id = $category?->id;
        $bookmark->url = $url;
        $bookmark->title = $title !== null ? Str::limit($title, self::getTitleMaxLength(), '') : null;
        $bookmark->description = $description !== null ? Str::limit($description, self::getDescriptionMaxLength(), '') : null;
        $bookmark->image = $image;

        $bookmark->save();

        return $bookmark;
    }

    /**
     * Sync tags for a Bookmark.
     */
    public function syncTags(Bookmark $bookmark, array $tags = []): void
    {
        $bookmark->tags()->sync($tags);
    }

    /**
     * Update the favorite status of a Bookmark.
     */
    public function updateFavoriteStatus(Bookmark $bookmark, bool $favorite): void
    {
        $bookmark->favorite = $favorite;
        $bookmark->save();
    }

    /**
     * Delete a Bookmark record.
     */
    public function delete(Bookmark $bookmark): bool
    {
        return (bool) $bookmark->delete();
    }

    /**
     * Update fetched metadata fields on an existing Bookmark.
     */
    public function updateMetadata(Bookmark $bookmark, ?string $title, ?string $description, ?string $image): Bookmark
    {
        $bookmark->title = $title;
        $bookmark->description = $description;
        $bookmark->image = $image;

        $bookmark->save();

        return $bookmark;
    }

    /**
     * Update only the image field for an existing Bookmark.
     */
    public function updateImage(Bookmark $bookmark, ?string $image): Bookmark
    {
        $bookmark->image = $image;

        $bookmark->save();

        return $bookmark;
    }
}
