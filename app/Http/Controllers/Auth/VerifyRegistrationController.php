<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyRegistrationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(\App\Http\Requests\Auth\VerifyRegistrationOTPRequest $request)
    {
        $email = $request->session()->get('registration_email');
        if (! $email) {
            return redirect()->route('register');
        }

        $otp = $request->safe()->string('otp');

        $otpInstance = new \Ichtrojan\Otp\Otp;
        $validation = $otpInstance->validate($email, $otp);

        if (! $validation->status) {
            return back()->withErrors(['otp' => $validation->message]);
        }

        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->save();

            \Illuminate\Support\Facades\Auth::login($user);

            $user->notify(new \App\Notifications\Auth\WelcomeNotification);

            $request->session()->forget('registration_email');

            return redirect()->route('dashboard');
        }

        return redirect()->route('register');
    }
}
