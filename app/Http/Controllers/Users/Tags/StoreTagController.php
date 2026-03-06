<?php

namespace App\Http\Controllers\Users\Tags;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreTagRequest;
use App\Repositories\TagRepository;

class StoreTagController extends Controller
{
    public function __construct(
        protected TagRepository $tagRepository,
    ) {}

    /**
     * Store a newly created resource in storage.
     */
    public function __invoke(StoreTagRequest $request)
    {
        $validated = $request->safe();
        $user = $request->user();

        $this->tagRepository->create(
            user: $user,
            name: $validated->string('name'),
            description: $validated->string('description') ?: null,
        );

        return redirect()->back()->with('success', 'Tag created successfully.');
    }
}
