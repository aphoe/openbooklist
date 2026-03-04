<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FetchBookmarkMetadataController extends Controller
{
    public function __construct(protected BookmarkService $bookmarkService) {}

    /**
     * Fetch metadata (title, description, image) for a given URL.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        try {
            $metadata = $this->bookmarkService->fetchMetadata(
                $request->input('url')
            );

            return response()->json([
                'success' => true,
                'data' => $metadata,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'data' => [
                    'title' => null,
                    'description' => null,
                    'image' => null,
                ],
                'message' => 'Could not fetch metadata for the given URL.',
            ]);
        }
    }
}
