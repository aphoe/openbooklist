<?php

namespace App\Http\Controllers\Users\Tags;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;

class DeleteTagController extends Controller
{
    public function __construct(
        protected TagRepository $tagRepository,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Tag $tag)
    {
        if ($request->user()->id !== $tag->user_id) {
            abort(403);
        }

        $this->tagRepository->delete($tag);

        return redirect()->back()->with('info', 'Tag deleted successfully.');
    }
}
