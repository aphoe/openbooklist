<?php

namespace App\Http\Controllers\Api\V1\Ext;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FetchTagsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Collection
    {
        return Tag::query()
            ->orderBy('name')
            ->pluck('name', 'slug');
    }
}
