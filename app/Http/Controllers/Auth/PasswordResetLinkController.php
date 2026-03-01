<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use App\Models\User;
use App\Notifications\Auth\SendOTPNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Log;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     */
    public function __invoke(PasswordResetLinkRequest $request)
    {
        $user = User::where('email', $request->safe()->string('email'))->first();

        if ($user) {
            $otp = (new Otp)->generate($user->email, 'numeric', 6, 60);

            Log::info("Generated OTP for {$user->email}: {$otp->token}");

            $user->notify(new SendOTPNotification($otp->token));
        }

        // Always save email to session and redirect to verify form
        $request->session()->put('password_reset_email', $request->safe()->string('email'));
        
        return redirect()->route('password.verify.form');
    }
}
