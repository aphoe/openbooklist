<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyRegistrationResendController extends Controller
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

        $user = \App\Models\User::where('email', $email)->first();

        if ($user) {
            $otp = (new \Ichtrojan\Otp\Otp)->generate($user->email, 'numeric', 6, 60);
            \Illuminate\Support\Facades\Log::info("Generated Registration OTP for {$user->email}: {$otp->token}");
            $user->notify(new \App\Notifications\Auth\SendOTPNotification($otp->token));
        }

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
