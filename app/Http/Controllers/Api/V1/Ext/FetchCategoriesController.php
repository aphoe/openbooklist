<?php

namespace App\Http\Controllers\Api\V1\Ext;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FetchCategoriesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->pluck('name', 'slug');
    }
}
