<?php

namespace App\Http\Controllers\Users\Tags;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateTagRequest;
use App\Models\Tag;
use App\Repositories\TagRepository;

class UpdateTagController extends Controller
{
    public function __construct(
        protected TagRepository $tagRepository,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->safe();
        $user = $request->user();

        if ($user->id !== $tag->user_id) {
            abort(403);
        }

        $this->tagRepository->update(
            tag: $tag,
            user: $user,
            name: $validated->string('name'),
            description: $validated->string('description') ?: null,
        );

        return redirect()->back()->with('success', 'Tag updated successfully.');
    }
}
