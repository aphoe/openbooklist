<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewPasswordViewController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function __invoke(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return inertia('Auth/ResetPassword', [
            'email' => $email,
            'token' => $request->route('token'),
        ]);
    }
}
