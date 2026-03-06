<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle the incoming search request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $query = $request->query('q', '');

        if (empty(trim($query))) {
            return inertia('Users/Search/Index', [
                'query' => $query,
                'bookmarks' => [],
                'categories' => [],
                'tags' => [],
                'tab' => $request->query('tab', 'all'),
            ]);
        }

        // Bookmark relevance search
        $bookmarks = Bookmark::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                // simple search
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('url', 'like', "%{$query}%");
            })
            ->with(['category', 'tags'])
            // Basic relevance mimicking
            ->orderByRaw('CASE 
                WHEN title LIKE ? THEN 1 
                WHEN description LIKE ? THEN 2 
                ELSE 3 END ASC', ["{$query}%", "{$query}%"])
            ->get();

        // Categories relevance search
        $categories = Category::where('user_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->withCount('bookmarks')
            ->orderByRaw('CASE 
                WHEN name LIKE ? THEN 1 
                ELSE 2 END ASC', ["{$query}%"])
            ->get();

        // Tags relevance search
        $tags = Tag::where('user_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->withCount('bookmarks')
            ->orderByRaw('CASE 
                WHEN name LIKE ? THEN 1 
                ELSE 2 END ASC', ["{$query}%"])
            ->get();

        return inertia('Users/Search/Index', [
            'query' => $query,
            'bookmarks' => $bookmarks,
            'categories' => $categories,
            'tags' => $tags,
            'tab' => $request->query('tab', 'all'),
        ]);
    }
}
