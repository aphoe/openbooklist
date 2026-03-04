<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display the bookmarks index.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(32);

        return inertia('Bookmark', [
            'bookmarks' => $bookmarks,
            'allCategories' => Category::where('user_id', $user->id)->orderBy('name')->get(['id', 'name']),
            'allTags' => Tag::where('user_id', $user->id)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
