<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display the bookmarks index.
     */
    public function __invoke(Request $request)
    {
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(32);

        return inertia('Bookmark', [
            'bookmarks' => $bookmarks,
        ]);
    }
}
