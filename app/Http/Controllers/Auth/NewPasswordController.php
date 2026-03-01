<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use App\Models\User;
use App\Notifications\Auth\PasswordChangedNotification;
use Illuminate\Support\Facades\Hash;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     */
    public function __invoke(NewPasswordRequest $request)
    {
        $email = $request->session()->get('password_reset_email', $request->safe()->string('email'));

        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->safe()->string('password')),
        ])->save();

        $user->notify(new PasswordChangedNotification);

        $request->session()->forget('password_reset_email');

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
