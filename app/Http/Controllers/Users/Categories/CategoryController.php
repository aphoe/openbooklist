<?php

namespace App\Http\Controllers\Users\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display the categories index.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $categories = Category::where('user_id', $user->id)
            ->withCount('bookmarks')
            ->orderBy('name')
            ->paginate(32)
            ->withQueryString();

        return inertia('Users/Categories/Index', [
            'categories' => $categories,
        ]);
    }
}
