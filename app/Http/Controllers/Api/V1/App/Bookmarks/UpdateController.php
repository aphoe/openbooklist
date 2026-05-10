<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\App\UpdateBookmarkRequest;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UpdateController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    public function __invoke(UpdateBookmarkRequest $request, Bookmark $bookmark): JsonResponse
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->safe();
        $user = $request->user();

        // Optional image handling
        $imagePath = $bookmark->image;
        if ($validated->string('image')->isNotEmpty() && $validated->string('image')->value() !== $bookmark->image) {
            $imagePath = $this->bookmarkService->downloadAndResizeImage(
                $validated->string('image')
            );
        }

        // Handle Category by slug
        $category = null;
        if ($validated->string('category')->isNotEmpty()) {
            $category = Category::where('user_id', $user->id)
                ->where('slug', $validated->string('category')->toString())
                ->first();
        }

        $bookmark = $this->bookmarkRepository->update(
            bookmark: $bookmark,
            user: $user,
            url: $validated->string('url'),
            category: $category,
            title: $validated->string('title') ?: null,
            description: $validated->string('description') ?: null,
            image: $imagePath,
        );

        // Sync tags if provided
        if ($validated->array('tags')) {
            $tagIds = [];
            foreach ($validated->array('tags') as $tagSlug) {
                if (empty(trim($tagSlug))) {
                    continue;
                }

                $tag = Tag::firstOrCreate(
                    ['slug' => $tagSlug, 'user_id' => $user->id],
                    ['name' => Str::headline($tagSlug)]
                );

                $tagIds[] = $tag->id;
            }

            if (count($tagIds) > 0) {
                $this->bookmarkRepository->syncTags($bookmark, $tagIds);
            }
        }

        return response()->json([
            'message' => 'Bookmark updated successfully.',
        ]);
    }
}
