<?php

namespace App\Http\Controllers\Users\Bookmarks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SetBookmarkImageRequest;
use App\Models\Bookmark;
use App\Repositories\BookmarkRepository;
use App\Services\BookmarkService;
use Illuminate\Http\RedirectResponse;

class SetBookmarkImageController extends Controller
{
    public function __construct(
        protected BookmarkRepository $bookmarkRepository,
        protected BookmarkService $bookmarkService,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(SetBookmarkImageRequest $request, Bookmark $bookmark): RedirectResponse
    {
        $previousImagePath = $bookmark->image;
        $imageSource = $request->safe()->string('image_source')->toString();

        if ($imageSource === 'screenshot') {
            $imagePath = $this->bookmarkService->takeWebsiteScreenshot($bookmark->url);

            if ($imagePath === null) {
                return redirect()->back()->with('error', 'Failed to capture website screenshot for this bookmark.');
            }
        } else {
            $imageUrl = $request->safe()->string('image_url')->toString();
            $imagePath = $this->bookmarkService->downloadAndResizeImage($imageUrl);

            if ($imagePath === null) {
                return redirect()->back()->with('error', 'Failed to download image from the provided URL.');
            }
        }

        $this->bookmarkRepository->updateImage($bookmark, $imagePath);

        if (is_string($previousImagePath) && $previousImagePath !== '' && $previousImagePath !== $imagePath) {
            $this->bookmarkService->deleteStoredImage($previousImagePath);
        }

        return redirect()->back()->with('success', 'Bookmark image updated successfully.');
    }
}
