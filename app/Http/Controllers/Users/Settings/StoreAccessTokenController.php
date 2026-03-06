<?php

namespace App\Http\Controllers\Users\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Settings\StoreAccessTokenRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreAccessTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreAccessTokenRequest $request): RedirectResponse
    {
        $name = $request->safe()->string('name');

        $abilities = $request->safe()->array('abilities');
        if (empty($abilities)) {
            $abilities = ['*'];
        }

        $expiresIn = $request->safe()->integer('expires_in');
        $expiresAt = $expiresIn > 0 ? now()->addDays($expiresIn) : null;

        $token = $request->user()->createToken($name, $abilities, $expiresAt);

        return redirect()->back()->with('newToken', $token->plainTextToken);
    }
}
