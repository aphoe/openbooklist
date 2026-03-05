<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class RecentlySavedController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with(['category', 'tags'])
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        $categories = Category::where('user_id', $user->id)
            ->withCount('bookmarks')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $tags = Tag::where('user_id', $user->id)
            ->withCount('bookmarks')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return inertia('Users/RecentlySaved/Index', [
            'bookmarks' => $bookmarks,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
