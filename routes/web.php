<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginViewController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\NewPasswordViewController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\PasswordResetLinkViewController;
use App\Http\Controllers\Auth\VerifyOTPController;
use App\Http\Controllers\Auth\VerifyOTPViewController;
use App\Http\Controllers\Users\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginViewController::class)->name('login');
Route::post('/login', LoginController::class);

Route::get('/register', \App\Http\Controllers\Auth\RegisterViewController::class)
    ->name('register');
Route::post('/register', \App\Http\Controllers\Auth\RegisterController::class);

Route::get('/verify-registration', \App\Http\Controllers\Auth\VerifyRegistrationOTPViewController::class)
    ->name('verification.notice');
Route::post('/verify-registration', \App\Http\Controllers\Auth\VerifyRegistrationController::class)
    ->name('verification.verify');
Route::post('/verify-registration/resend', \App\Http\Controllers\Auth\VerifyRegistrationResendController::class)
    ->name('verification.send');

Route::get('/forgot-password', PasswordResetLinkViewController::class)
    ->name('password.request');
Route::post('/forgot-password', PasswordResetLinkController::class)
    ->name('password.email');

Route::get('/verify-otp', VerifyOTPViewController::class)
    ->name('password.verify.form');
Route::post('/verify-otp', VerifyOTPController::class)
    ->name('password.verify.otp');

Route::get('/reset-password/{token}', NewPasswordViewController::class)
    ->name('password.reset');
Route::post('/reset-password', NewPasswordController::class)
    ->name('password.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', LogoutController::class)->name('logout');
});
