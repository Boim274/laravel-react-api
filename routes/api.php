<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// post
Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);

// fishing
Route::apiResource('/fishings', App\Http\Controllers\Api\FishingController::class);
