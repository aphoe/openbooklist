<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetLinkViewController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function __invoke(Request $request)
    {
        return inertia('Auth/ForgotPassword');
    }
}
