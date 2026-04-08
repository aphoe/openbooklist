<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1/ext')->group(function () {
    Route::middleware(\App\Http\Middleware\CheckReadAbility::class)->group(function () {
        Route::get('/categories', \App\Http\Controllers\Api\V1\Ext\FetchCategoriesController::class);
        Route::get('/tags', \App\Http\Controllers\Api\V1\Ext\FetchTagsController::class);
    });

    Route::middleware(\App\Http\Middleware\CheckWriteAbility::class)->group(function () {
        Route::post('/bookmarks', \App\Http\Controllers\Api\V1\Ext\CreateController::class);
    });
});
