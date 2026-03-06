<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyOTPViewController extends Controller
{
    /**
     * Display the OTP verification view.
     */
    public function __invoke(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return inertia('Auth/VerifyOTP', [
            'email' => $email,
        ]);
    }
}
