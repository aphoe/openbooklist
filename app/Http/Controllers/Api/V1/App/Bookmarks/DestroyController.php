<?php

namespace App\Http\Controllers\Api\V1\App\Bookmarks;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DestroyController extends Controller
{
    public function __invoke(Request $request, Bookmark $bookmark): Response
    {
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $bookmark->delete();

        return response()->noContent();
    }
}
