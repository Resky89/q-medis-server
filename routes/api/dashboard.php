<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
