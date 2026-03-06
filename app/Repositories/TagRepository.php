<?php

namespace App\Repositories;

use App\Managers\ModelManager;
use App\Models\Tag;
use App\Models\User;

final class TagRepository
{
    /**
     * Create a new Tag record.
     */
    public function create(
        User $user,
        string $name,
        ?string $description = null
    ): Tag {
        $tag = new Tag;

        $tag->user_id = $user->id;
        $tag->name = $name;
        $tag->slug = (new ModelManager)->generateUniqueSlug('tags', $name);
        $tag->description = $description;

        $tag->save();

        return $tag;
    }

    /**
     * Update an existing Tag record.
     */
    public function update(
        Tag $tag,
        User $user,
        string $name,
        ?string $description = null
    ): Tag {
        $tag->user_id = $user->id;

        $tag->name = $name;
        $tag->description = $description;

        $tag->save();

        return $tag;
    }

    /**
     * Delete a Tag record.
     */
    public function delete(Tag $tag): bool
    {
        return (bool) $tag->delete();
    }
}
