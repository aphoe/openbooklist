<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyRegistrationOTPViewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $email = $request->session()->get('registration_email');

        if (! $email) {
            return redirect()->route('register');
        }

        return inertia('Auth/VerifyRegistrationOTP', [
            'email' => $email,
        ]);
    }
}
