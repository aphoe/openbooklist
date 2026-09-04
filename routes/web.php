<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', \App\Http\Controllers\Auth\LoginViewController::class)->name('login');
Route::post('/login', \App\Http\Controllers\Auth\LoginController::class);

Route::get('/register', \App\Http\Controllers\Auth\RegisterViewController::class)
    ->name('register');
Route::post('/register', \App\Http\Controllers\Auth\RegisterController::class);

Route::get('/verify-registration', \App\Http\Controllers\Auth\VerifyRegistrationOTPViewController::class)
    ->name('verification.notice');
Route::post('/verify-registration', \App\Http\Controllers\Auth\VerifyRegistrationController::class)
    ->name('verification.verify');
Route::post('/verify-registration/resend', \App\Http\Controllers\Auth\VerifyRegistrationResendController::class)
    ->name('verification.send');

Route::get('/forgot-password', \App\Http\Controllers\Auth\PasswordResetLinkViewController::class)
    ->name('password.request');
Route::post('/forgot-password', \App\Http\Controllers\Auth\PasswordResetLinkController::class)
    ->name('password.email');

Route::get('/verify-otp', \App\Http\Controllers\Auth\VerifyOTPViewController::class)
    ->name('password.verify.form');
Route::post('/verify-otp', \App\Http\Controllers\Auth\VerifyOTPController::class)
    ->name('password.verify.otp');

Route::get('/reset-password/{token}', \App\Http\Controllers\Auth\NewPasswordViewController::class)
    ->name('password.reset');
Route::post('/reset-password', \App\Http\Controllers\Auth\NewPasswordController::class)
    ->name('password.store');

Route::middleware('auth')->group(function () {
    Route::get('/', \App\Http\Controllers\Users\Bookmarks\BookmarkController::class)->name('dashboard');
    Route::get('/recently-saved', \App\Http\Controllers\Users\RecentlySavedController::class)->name('recently-saved');
    Route::get('/search', \App\Http\Controllers\Users\SearchController::class)->name('search');

    Route::post('/bookmarks', \App\Http\Controllers\Users\Bookmarks\StoreBookmarkController::class)->name('bookmarks.store');
    Route::put('/bookmarks/{bookmark}', \App\Http\Controllers\Users\Bookmarks\UpdateBookmarkController::class)->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', \App\Http\Controllers\Users\Bookmarks\DeleteBookmarkController::class)->name('bookmarks.destroy');
    Route::post('/bookmarks/{bookmark}/favorite', \App\Http\Controllers\Users\Bookmarks\ToggleFavoriteController::class)->name('bookmarks.favorite');
    Route::post('/bookmarks/{bookmark}/set-image', \App\Http\Controllers\Users\Bookmarks\SetBookmarkImageController::class)->name('bookmarks.set-image');
    Route::post('/bookmarks/{bookmark}/refetch-metadata', \App\Http\Controllers\Users\Bookmarks\RefetchBookmarkMetadataController::class)->name('bookmarks.refetch-metadata');
    Route::post('/bookmarks/fetch-metadata', \App\Http\Controllers\Users\Bookmarks\FetchBookmarkMetadataController::class)->name('bookmarks.fetch-metadata');
    Route::post('/bookmarks/per-page', \App\Http\Controllers\Users\Bookmarks\UpdateBookmarkPerPageController::class)->name('bookmarks.per-page');

    Route::get('/categories', \App\Http\Controllers\Users\Categories\CategoryController::class)->name('categories.index');
    Route::post('/categories', \App\Http\Controllers\Users\Categories\StoreCategoryController::class)->name('categories.store');
    Route::put('/categories/{category}', \App\Http\Controllers\Users\Categories\UpdateCategoryController::class)->name('categories.update');
    Route::delete('/categories/{category}', \App\Http\Controllers\Users\Categories\DeleteCategoryController::class)->name('categories.destroy');
    Route::post('/categories/per-page', \App\Http\Controllers\Users\Categories\UpdateCategoryPerPageController::class)->name('categories.per-page');

    Route::get('/tags', \App\Http\Controllers\Users\Tags\TagController::class)->name('tags.index');
    Route::post('/tags', \App\Http\Controllers\Users\Tags\StoreTagController::class)->name('tags.store');
    Route::put('/tags/{tag}', \App\Http\Controllers\Users\Tags\UpdateTagController::class)->name('tags.update');
    Route::delete('/tags/{tag}', \App\Http\Controllers\Users\Tags\DeleteTagController::class)->name('tags.destroy');

    Route::post('/logout', \App\Http\Controllers\Auth\LogoutController::class)->name('logout');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', \App\Http\Controllers\Users\Settings\IndexController::class)->name('index');
        Route::put('/general', \App\Http\Controllers\Users\Settings\UpdateGeneralController::class)->name('general');
        Route::put('/password', \App\Http\Controllers\Users\Settings\UpdatePasswordController::class)->name('password');
        Route::put('/ai', \App\Http\Controllers\Users\Settings\UpdateAiConfigController::class)->name('ai');
        Route::post('/tokens', \App\Http\Controllers\Users\Settings\StoreAccessTokenController::class)->name('tokens.store');
        Route::delete('/tokens/{token}', \App\Http\Controllers\Users\Settings\DestroyAccessTokenController::class)->name('tokens.destroy');
    });
});

Route::prefix('demo')
    ->group(function () {
        Route::get('/playground', [\App\Http\Controllers\DemoController::class, 'playground']);
    });
