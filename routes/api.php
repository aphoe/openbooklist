<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1/ext')->group(function () {
    Route::get('/categories', \App\Http\Controllers\Api\V1\Ext\FetchCategoriesController::class);
    Route::get('/tags', \App\Http\Controllers\Api\V1\Ext\FetchTagsController::class);
});
