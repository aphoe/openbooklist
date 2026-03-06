<?php

namespace App\Http\Controllers\Users\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class DestroyAccessTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, PersonalAccessToken $token): RedirectResponse
    {
        if ($token->tokenable_id !== $request->user()->id) {
            abort(403);
        }

        $token->delete();

        return redirect()->back();
    }
}
