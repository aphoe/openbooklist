<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Repositories\BookmarkRepository;
use Illuminate\Http\Request;

class ToggleFavoriteController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
    ) {}

    /**
     * Toggle the favorite status of the specified bookmark.
     */
    public function __invoke(Request $request, Bookmark $bookmark)
    {
        if ($request->user()->id !== $bookmark->user_id) {
            abort(403);
        }

        $this->bookmarkRepository->updateFavoriteStatus($bookmark, !$bookmark->favorite);

        return redirect()->back();
    }
}
