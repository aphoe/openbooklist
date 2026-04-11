<?php

namespace App\Http\Controllers\Users\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Settings\UpdateGeneralRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;

class UpdateGeneralController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateGeneralRequest $request, UserRepository $repository): RedirectResponse
    {
        $language = trim((string) $request->safe()->string('language'));

        $repository->updateProfile(
            $request->user(),
            $request->safe()->string('first_name'),
            $request->safe()->string('last_name'),
            $request->safe()->string('email'),
            $language !== '' ? $language : 'en',
        );

        return back()->with('success', 'Profile information updated successfully.');
    }
}
