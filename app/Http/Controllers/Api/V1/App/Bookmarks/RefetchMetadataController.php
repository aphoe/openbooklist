<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RefetchBookmarkMetadataRequest;
use App\Models\Bookmark;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;

class RefetchMetadataController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    public function __invoke(RefetchBookmarkMetadataRequest $request, Bookmark $bookmark): JsonResponse
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $metadata = $this->bookmarkService->fetchMetadata($bookmark->url, $request->user());

        $imagePath = $bookmark->image;

        if (! empty($metadata['image'])) {
            $downloadedImage = $this->bookmarkService->downloadAndResizeImage($metadata['image']);

            if ($downloadedImage !== null) {
                $imagePath = $downloadedImage;
            }
        }

        $this->bookmarkRepository->updateMetadata(
            bookmark: $bookmark,
            title: $metadata['title'] ?: $bookmark->title,
            description: $metadata['description'] ?: $bookmark->description,
            image: $imagePath,
        );

        return response()->json([
            'message' => 'Bookmark metadata refreshed successfully.',
        ]);
    }
}
