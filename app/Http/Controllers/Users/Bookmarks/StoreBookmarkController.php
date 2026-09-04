<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreBookmarkRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;

class StoreBookmarkController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    /**
     * Store a new bookmark.
     */
    public function __invoke(StoreBookmarkRequest $request)
    {
        $validated = $request->safe();
        $user = $request->user();
        $url = $validated->string('url');

        // Check for duplicate URL
        if ($this->bookmarkService->urlExistsForUser($user, $url)) {
            return redirect()->back()->withErrors(['url' => 'A bookmark with this URL already exists.']);
        }

        // Download and resize image if provided
        $imagePath = null;
        if ($validated->string('image')->isNotEmpty()) {
            $imagePath = $this->bookmarkService->downloadAndResizeImage(
                $validated->string('image')
            );
        }

        $category = $validated->integer('category_id')
            ? Category::find($validated->integer('category_id'))
            : null;

        $bookmark = $this->bookmarkRepository->create(
            user: $user,
            url: $url,
            category: $category,
            title: $this->bookmarkService->cleanTitle($validated->string('title') ?: null, $url),
            description: $validated->string('description') ?: null,
            image: $imagePath,
        );

        // Sync tags if provided
        if ($validated->array('tags')) {
            $tagIds = [];
            foreach ($validated->array('tags') as $tagSlug) {
                if (empty(trim($tagSlug))) {
                    continue;
                }

                $tag = Tag::firstOrCreate(
                    ['slug' => $tagSlug, 'user_id' => $user->id],
                    ['name' => $tagSlug]
                );

                $tagIds[] = $tag->id;
            }

            if (count($tagIds) > 0) {
                $this->bookmarkRepository->syncTags($bookmark, $tagIds);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Bookmark saved successfully.');
    }
}
