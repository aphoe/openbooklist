<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOTPRequest;
use Ichtrojan\Otp\Otp;

class VerifyOTPController extends Controller
{
    /**
     * Verify the provided OTP.
     */
    public function __invoke(VerifyOTPRequest $request)
    {
        $email = $request->session()->get('password_reset_email', $request->safe()->string('email'));
        $otp = $request->safe()->string('otp');

        $otpInstance = new Otp;
        $validation = $otpInstance->validate($email, $otp);

        if (! $validation->status) {
            return back()->withErrors(['otp' => $validation->message])
                ->withInput($request->only('email'));
        }

        // Validation successful, pass data to reset password form
        return redirect()->route('password.reset', [
            'token' => $otp,
        ]);
    }
}
