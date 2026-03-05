<?php

namespace App\Http\Controllers\Users\Tags;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $sort = $request->input('sort', 'alphabetical');
        $search = $request->input('search');

        $query = Tag::where('user_id', $user->id)
            ->withCount('bookmarks');

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        if ($sort === 'popularity') {
            $query->orderByDesc('bookmarks_count')->orderBy('name', 'asc');
        } elseif ($sort === 'recent') {
            $query->latest();
        } else {
            $query->orderBy('name', 'asc');
        }

        $tags = $query->paginate(50)->withQueryString();

        return inertia('Users/Tags/Index', [
            'tags' => $tags,
        ]);
    }
}
