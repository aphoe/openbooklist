<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\RedirectResponse;

class UpdateCategoryController extends Controller
{
    public function __invoke(Category $category, UpdateCategoryRequest $request, CategoryRepository $repository): RedirectResponse
    {
        abort_if($category->user_id !== $request->user()->id, 403);

        $repository->update(
            category: $category,
            user: $request->user(),
            name: $request->safe()->string('name')->value(),
            description: $request->safe()->string('description')->value()
        );

        return back()->with('success', 'Category updated successfully.');
    }
}
