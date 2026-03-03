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
        ?string $image = null,
        bool $isFavorite = false,
        string $aiSummaryStatus = 'pending'
    ): Bookmark {
        $bookmark = new Bookmark;

        $bookmark->user_id = $user->id;
        $bookmark->category_id = $category?->id;
        $bookmark->url = $url;
        $bookmark->title = $title;
        $bookmark->description = $description;
        $bookmark->image = $image;
        $bookmark->is_favorite = $isFavorite;

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
        ?string $image = null,
        bool $isFavorite = false,
        string $aiSummaryStatus = 'pending'
    ): Bookmark {
        $bookmark->user_id = $user->id;
        $bookmark->category_id = $category?->id;
        $bookmark->url = $url;
        $bookmark->title = $title;
        $bookmark->description = $description;
        $bookmark->image = $image;
        $bookmark->is_favorite = $isFavorite;
        $bookmark->ai_summary_status = $aiSummaryStatus;

        $bookmark->save();

        return $bookmark;
    }

    /**
     * Delete a Bookmark record.
     */
    public function delete(Bookmark $bookmark): bool
    {
        return (bool) $bookmark->delete();
    }
}
