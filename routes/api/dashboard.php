<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
