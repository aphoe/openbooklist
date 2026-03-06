<?php

namespace App\Http\Controllers\Users\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Settings\UpdateAiConfigRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;

class UpdateAiConfigController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateAiConfigRequest $request, UserRepository $repository): RedirectResponse
    {
        $repository->updateAiDescription($request->user(), $request->safe()->boolean('use_ai_description'));
        $repository->updateAiModel($request->user(), $request->safe()->string('ai_model') ?: null);

        return back()->with('success', 'AI Configuration updated successfully.');
    }
}
