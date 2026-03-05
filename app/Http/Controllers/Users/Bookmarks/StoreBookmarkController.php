<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreBookmarkRequest;
use App\Models\Category;
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
            url: $validated->string('url'),
            category: $category,
            title: $validated->string('title') ?: null,
            description: $validated->string('description') ?: null,
            image: $imagePath,
        );

        // Sync tags if provided
        if ($validated->array('tags')) {
            $this->bookmarkRepository->syncTags($bookmark, $validated->array('tags'));
        }

        return redirect()->route('dashboard')->with('status', 'Bookmark saved successfully.');
    }
}
