<?php

namespace App\Repositories;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\User;

final class BookmarkRepository
{
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
        $bookmark->title = $title;
        $bookmark->description = $description;
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
        $bookmark->title = $title;
        $bookmark->description = $description;
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
}
