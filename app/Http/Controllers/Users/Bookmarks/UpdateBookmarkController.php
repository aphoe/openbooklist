<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateBookmarkRequest;
use App\Models\Bookmark;
use App\Models\Category;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;

class UpdateBookmarkController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    /**
     * Update the specified bookmark.
     */
    public function __invoke(UpdateBookmarkRequest $request, Bookmark $bookmark)
    {
        $validated = $request->safe();
        $user = $request->user();

        // Optional image handling
        $imagePath = $bookmark->image;
        if ($validated->string('image')->isNotEmpty() && $validated->string('image')->value() !== $bookmark->image) {
            $imagePath = $this->bookmarkService->downloadAndResizeImage(
                $validated->string('image')
            );
        }

        $category = $validated->integer('category_id')
            ? Category::find($validated->integer('category_id'))
            : null;

        $bookmark = $this->bookmarkRepository->update(
            bookmark: $bookmark,
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

        return redirect()->back()->with('status', 'Bookmark updated successfully.');
    }
}
