<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RefetchBookmarkMetadataRequest;
use App\Models\Bookmark;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;

class RefetchBookmarkMetadataController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(RefetchBookmarkMetadataRequest $request, Bookmark $bookmark)
    {
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

        return redirect()->back()->with('success', 'Bookmark metadata refreshed successfully.');
    }
}
