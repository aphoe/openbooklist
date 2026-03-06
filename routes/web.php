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
use App\Http\Controllers\Users\Bookmarks\BookmarkController;
use App\Http\Controllers\Users\Bookmarks\DeleteBookmarkController;
use App\Http\Controllers\Users\Bookmarks\FetchBookmarkMetadataController;
use App\Http\Controllers\Users\Bookmarks\StoreBookmarkController;
use App\Http\Controllers\Users\Bookmarks\ToggleFavoriteController;
use App\Http\Controllers\Users\Bookmarks\UpdateBookmarkController;
use App\Http\Controllers\Users\Categories\CategoryController;
use App\Http\Controllers\Users\Categories\DeleteCategoryController;
use App\Http\Controllers\Users\Categories\StoreCategoryController;
use App\Http\Controllers\Users\Categories\UpdateCategoryController;
use App\Http\Controllers\Users\RecentlySavedController;
use App\Http\Controllers\Users\SearchController;
use App\Http\Controllers\Users\Tags\DeleteTagController;
use App\Http\Controllers\Users\Tags\StoreTagController;
use App\Http\Controllers\Users\Tags\TagController;
use App\Http\Controllers\Users\Tags\UpdateTagController;
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
    Route::get('/recently-saved', RecentlySavedController::class)->name('recently-saved');
    Route::get('/search', SearchController::class)->name('search');

    Route::post('/bookmarks', StoreBookmarkController::class)->name('bookmarks.store');
    Route::put('/bookmarks/{bookmark}', UpdateBookmarkController::class)->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', DeleteBookmarkController::class)->name('bookmarks.destroy');
    Route::post('/bookmarks/{bookmark}/favorite', ToggleFavoriteController::class)->name('bookmarks.favorite');
    Route::post('/bookmarks/fetch-metadata', FetchBookmarkMetadataController::class)->name('bookmarks.fetch-metadata');

    Route::get('/categories', CategoryController::class)->name('categories.index');
    Route::post('/categories', StoreCategoryController::class)->name('categories.store');
    Route::put('/categories/{category}', UpdateCategoryController::class)->name('categories.update');
    Route::delete('/categories/{category}', DeleteCategoryController::class)->name('categories.destroy');

    Route::get('/tags', TagController::class)->name('tags.index');
    Route::post('/tags', StoreTagController::class)->name('tags.store');
    Route::put('/tags/{tag}', UpdateTagController::class)->name('tags.update');
    Route::delete('/tags/{tag}', DeleteTagController::class)->name('tags.destroy');

    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::prefix('demo')
    ->group(function () {
        Route::get('/playground', [\App\Http\Controllers\DemoController::class, 'playground']);
    });
