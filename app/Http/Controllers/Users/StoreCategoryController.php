<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreCategoryRequest;
use App\Repositories\CategoryRepository;
use Illuminate\Http\RedirectResponse;

class StoreCategoryController extends Controller
{
    public function __invoke(StoreCategoryRequest $request, CategoryRepository $repository): RedirectResponse
    {
        $repository->create(
            user: $request->user(),
            name: $request->safe()->string('name')->value(),
            description: $request->safe()->string('description')->value()
        );

        return back()->with('success', 'Category created successfully.');
    }
}
