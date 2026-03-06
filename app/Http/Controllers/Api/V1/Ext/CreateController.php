<?php

namespace App\Http\Controllers\Api\V1\Ext;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Ext\CreateBookmarkRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CreateController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateBookmarkRequest $request): JsonResponse
    {
        $validated = $request->safe();
        $user = $request->user();
        $url = $validated->string('url')->toString();

        // Fetch Metadata
        $metadata = $this->bookmarkService->fetchMetadata($url);

        $imagePath = null;
        if (! empty($metadata['image'])) {
            $imagePath = $this->bookmarkService->downloadAndResizeImage($metadata['image']);
        }

        // Handle Category
        $category = null;
        if ($validated->string('category')->isNotEmpty()) {
            $category = Category::where('slug', $validated->string('category')->toString())->first();
        }

        // Create Bookmark
        $bookmark = $this->bookmarkRepository->create(
            user: $user,
            url: $url,
            category: $category,
            title: $metadata['title'] ?? null,
            description: $metadata['description'] ?? null,
            image: $imagePath,
        );

        // Handle Tags
        if ($validated->array('tag')) {
            $tagIds = [];
            foreach ($validated->array('tag') as $tagSlug) {
                // Ignore empty tags
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
            'message' => 'Bookmark saved successfully.',
        ], 201);
    }
}
