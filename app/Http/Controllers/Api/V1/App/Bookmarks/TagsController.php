<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TagsController extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return Tag::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->pluck('name', 'slug');
    }
}
