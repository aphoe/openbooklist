<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(\App\Http\Requests\Auth\RegisterRequest $request)
    {
        if (! config('project.multi_user')) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled.');
        }
        $user = \App\Models\User::create([
            'first_name' => $request->safe()->string('first_name'),
            'last_name' => $request->safe()->string('last_name'),
            'email' => $request->safe()->string('email'),
            'password' => \Illuminate\Support\Facades\Hash::make($request->safe()->string('password')),
        ]);

        $otp = (new \Ichtrojan\Otp\Otp)->generate($user->email, 'numeric', 6, 60);
        \Illuminate\Support\Facades\Log::info("Generated Registration OTP for {$user->email}: {$otp->token}");

        $user->notify(new \App\Notifications\Auth\SendOTPNotification($otp->token));

        $request->session()->put('registration_email', $user->email);

        return redirect()->route('verification.notice');
    }
}
