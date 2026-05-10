<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoriesController extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Category::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
