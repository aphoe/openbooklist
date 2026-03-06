<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterViewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (! config('project.multi_user')) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled.');
        }

        return inertia('Auth/Register');
    }
}
