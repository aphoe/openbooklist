<?php

use App\Http\Middleware\CheckReadAbility;
use App\Http\Middleware\CheckWriteAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1/app')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', \App\Http\Controllers\Api\V1\App\Auth\LoginController::class);
    });

    Route::prefix('bookmarks')->group(function () {
        Route::get('/', \App\Http\Controllers\Api\V1\App\Bookmarks\IndexController::class);
        Route::post('/', \App\Http\Controllers\Api\V1\App\Bookmarks\CreateController::class);
        Route::get('/categories', \App\Http\Controllers\Api\V1\App\Bookmarks\CategoriesController::class);
        Route::get('/tags', \App\Http\Controllers\Api\V1\App\Bookmarks\TagsController::class);
        Route::get('/{bookmark}', \App\Http\Controllers\Api\V1\App\Bookmarks\ShowController::class);
        Route::put('/{bookmark}', \App\Http\Controllers\Api\V1\App\Bookmarks\UpdateController::class);
        Route::delete('/{bookmark}', \App\Http\Controllers\Api\V1\App\Bookmarks\DestroyController::class);
        Route::post('/{bookmark}/refetch-metadata', \App\Http\Controllers\Api\V1\App\Bookmarks\RefetchMetadataController::class);
        Route::post('/{bookmark}/set-image', \App\Http\Controllers\Api\V1\App\Bookmarks\SetImageController::class);
    });
});

Route::middleware('auth:sanctum')->prefix('v1/ext')->group(function () {
    Route::middleware(CheckReadAbility::class)->group(function () {
        Route::get('/categories', \App\Http\Controllers\Api\V1\Ext\FetchCategoriesController::class);
        Route::get('/tags', \App\Http\Controllers\Api\V1\Ext\FetchTagsController::class);
    });

    Route::middleware(CheckWriteAbility::class)->group(function () {
        Route::post('/bookmarks', \App\Http\Controllers\Api\V1\Ext\CreateController::class);
    });
});
