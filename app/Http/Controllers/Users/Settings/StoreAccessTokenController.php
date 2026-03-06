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

        $token = $request->user()->createToken($name);

        return redirect()->back()->with('newToken', $token->plainTextToken);
    }
}
