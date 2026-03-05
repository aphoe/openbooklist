<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteCategoryController extends Controller
{
    public function __invoke(Category $category, Request $request, CategoryRepository $repository): RedirectResponse
    {
        abort_if($category->user_id !== $request->user()->id, 403);

        $repository->delete($category);

        return back()->with('success', 'Category deleted successfully.');
    }
}
