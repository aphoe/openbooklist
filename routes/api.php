<?php

use App\Http\Controllers\Api\V1\App\Auth\LoginController;
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
