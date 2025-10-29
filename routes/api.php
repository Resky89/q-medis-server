<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoketController;
use App\Http\Controllers\Api\AntrianController;
use App\Http\Controllers\Api\DisplayController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Public display endpoints
Route::prefix('display')->group(function () {
    Route::get('lokets', [DisplayController::class, 'lokets']);
    Route::get('lokets/{loket}', [DisplayController::class, 'show']);
});

// Protected resources
Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('lokets', LoketController::class);
    Route::apiResource('antrians', AntrianController::class)->only(['index','show','store','update']);
});
