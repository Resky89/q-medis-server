<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoketController;

// Read-only for any authenticated user (including petugas)
Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('lokets', LoketController::class)->only(['index', 'show']);
});

// Write operations restricted to admin
Route::middleware(['jwt.auth', 'role:admin'])->group(function () {
    Route::apiResource('lokets', LoketController::class)->only(['store', 'update', 'destroy']);
});
