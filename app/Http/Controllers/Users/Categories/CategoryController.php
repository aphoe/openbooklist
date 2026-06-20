<?php

namespace App\Http\Controllers\Users\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display the categories index.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $categoryService = new CategoryService();
        $perPage = $categoryService->getValidPerPage($request->input('per_page'));

        $categories = Category::where('user_id', $user->id)
            ->withCount('bookmarks')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return inertia('Users/Categories/Index', [
            'categories' => $categories,
            'paginationPresets' => $categoryService->getPaginationPresets(),
        ]);
    }
}
