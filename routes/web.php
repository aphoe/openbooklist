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
use App\Http\Controllers\Users\BookmarkController;
use App\Http\Controllers\Users\FetchBookmarkMetadataController;
use App\Http\Controllers\Users\StoreBookmarkController;
use App\Http\Controllers\Users\UpdateBookmarkController;
use App\Http\Controllers\Users\DeleteBookmarkController;
use App\Http\Controllers\Users\ToggleFavoriteController;
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
    Route::get('/', BookmarkController::class)->name('dashboard');
    Route::post('/bookmarks', StoreBookmarkController::class)->name('bookmarks.store');
    Route::put('/bookmarks/{bookmark}', UpdateBookmarkController::class)->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', DeleteBookmarkController::class)->name('bookmarks.destroy');
    Route::post('/bookmarks/{bookmark}/favorite', ToggleFavoriteController::class)->name('bookmarks.favorite');
    Route::post('/bookmarks/fetch-metadata', FetchBookmarkMetadataController::class)->name('bookmarks.fetch-metadata');
    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::prefix('demo')
    ->group(function () {
        Route::get('/playground', [\App\Http\Controllers\DemoController::class, 'playground']);
    });
