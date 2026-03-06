<?php

namespace App\Http\Controllers\Users\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Settings\UpdatePasswordRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;

class UpdatePasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdatePasswordRequest $request, UserRepository $repository): RedirectResponse
    {
        $repository->updatePassword($request->user(), $request->safe()->string('password'));

        return back()->with('success', 'Password updated successfully.');
    }
}
