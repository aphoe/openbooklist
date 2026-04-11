<?php

namespace App\Http\Controllers\Users\Bookmarks;

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
        $sort = $request->input('sort', 'newest');
        $categorySlug = $request->input('category');

        $query = Bookmark::where('user_id', $user->id)
            ->with(['category', 'tags']);

        if (is_string($categorySlug) && $categorySlug !== '') {
            $category = Category::where('user_id', $user->id)
                ->where('slug', $categorySlug)
                ->first();

            if ($category) {
                $query->where('category_id', $category->id);
            } else {
                $query->where('id', -1);
            }
        }

        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'alphabetical') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $bookmarks = $query->paginate(32)->withQueryString();

        return inertia('Users/Bookmarks/Index', [
            'bookmarks' => $bookmarks,
            'allCategories' => Category::where('user_id', $user->id)->orderBy('name')->get(['id', 'name']),
            'allTags' => Tag::where('user_id', $user->id)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
