<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TagsController extends Controller
{
    public function __invoke(Request $request)
    {
        $tagService = new TagService();
        $perPage = $tagService->getValidPerPage($request->input('per_page'));

        return Tag::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->paginate($perPage)
            ->through(fn ($tag) => [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]);
    }
}
