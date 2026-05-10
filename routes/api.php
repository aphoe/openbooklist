<?php

use App\Http\Controllers\Api\V1\App\Auth\LoginController;
use App\Http\Controllers\Api\V1\App\Bookmarks\CategoriesController;
use App\Http\Controllers\Api\V1\App\Bookmarks\CreateController as BookmarksCreateController;
use App\Http\Controllers\Api\V1\App\Bookmarks\IndexController as BookmarksIndexController;
use App\Http\Controllers\Api\V1\App\Bookmarks\ShowController;
use App\Http\Controllers\Api\V1\App\Bookmarks\TagsController;
use App\Http\Controllers\Api\V1\Ext\CreateController;
use App\Http\Controllers\Api\V1\Ext\FetchCategoriesController;
use App\Http\Controllers\Api\V1\Ext\FetchTagsController;
use App\Http\Middleware\CheckReadAbility;
use App\Http\Middleware\CheckWriteAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1/app')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', LoginController::class);
    });

    Route::prefix('bookmarks')->group(function () {
        Route::get('/', BookmarksIndexController::class);
        Route::post('/', BookmarksCreateController::class);
        Route::get('/{bookmark}', ShowController::class);
        Route::get('/categories', CategoriesController::class);
        Route::get('/tags', TagsController::class);
    });
});

Route::middleware('auth:sanctum')->prefix('v1/ext')->group(function () {
    Route::middleware(CheckReadAbility::class)->group(function () {
        Route::get('/categories', FetchCategoriesController::class);
        Route::get('/tags', FetchTagsController::class);
    });

    Route::middleware(CheckWriteAbility::class)->group(function () {
        Route::post('/bookmarks', CreateController::class);
    });
});
