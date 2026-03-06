<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginViewController extends Controller
{
    /**
     * Display the login view.
     */
    public function __invoke(Request $request)
    {
        return inertia('Auth/Login');
    }
}
