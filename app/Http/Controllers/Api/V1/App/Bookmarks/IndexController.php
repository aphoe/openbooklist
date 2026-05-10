<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexController extends Controller
{
    public function __invoke(Request $request): LengthAwarePaginator
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

        return $query->paginate(32)->withQueryString();
    }
}
