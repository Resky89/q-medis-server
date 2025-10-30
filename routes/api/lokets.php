<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoketController;

Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('lokets', LoketController::class);
});
