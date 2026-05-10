<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SetBookmarkImageRequest;
use App\Models\Bookmark;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;

class SetImageController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    public function __invoke(SetBookmarkImageRequest $request, Bookmark $bookmark): JsonResponse
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $previousImagePath = $bookmark->image;
        $imageSource = $request->safe()->string('image_source')->toString();

        if ($imageSource === 'screenshot') {
            $imagePath = $this->bookmarkService->takeWebsiteScreenshot($bookmark->url);

            if ($imagePath === null) {
                return response()->json([
                    'message' => 'Failed to capture website screenshot for this bookmark.',
                ], 400);
            }
        } else {
            $imageUrl = $request->safe()->string('image_url')->toString();
            $imagePath = $this->bookmarkService->downloadAndResizeImage($imageUrl);

            if ($imagePath === null) {
                return response()->json([
                    'message' => 'Failed to download image from the provided URL.',
                ], 400);
            }
        }

        $this->bookmarkRepository->updateImage($bookmark, $imagePath);

        if (is_string($previousImagePath) && $previousImagePath !== '' && $previousImagePath !== $imagePath) {
            $this->bookmarkService->deleteStoredImage($previousImagePath);
        }

        return response()->json([
            'message' => 'Bookmark image updated successfully.',
        ]);
    }
}
