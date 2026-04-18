<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;
use Illuminate\Http\Request;

class DeleteBookmarkController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    /**
     * Delete the specified bookmark.
     */
    public function __invoke(Request $request, Bookmark $bookmark)
    {
        if ($request->user()->id !== $bookmark->user_id) {
            abort(403);
        }

        $imagePath = $bookmark->image;

        if ($this->bookmarkRepository->delete($bookmark)) {
            $this->bookmarkService->deleteStoredImage($imagePath);
        }

        return redirect()->back()->with('info', 'Bookmark deleted successfully.');
    }
}
