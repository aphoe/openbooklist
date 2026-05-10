<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function __invoke(Request $request, Bookmark $bookmark)
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $bookmark->load(['category', 'tags']);

        return [
            'id' => $bookmark->id,
            'title' => $bookmark->title,
            'description' => $bookmark->description,
            'image_url' => $bookmark->image_url,
            'domain' => $bookmark->domain,
            'category' => $bookmark->category ? [$bookmark->category->id => $bookmark->category->name] : null,
            'tags' => $bookmark->tags->pluck('name', 'id')->toArray(),
            'date_added' => $bookmark->created_at->format('M d, Y \a\t h:i A'),
        ];
    }
}
