<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::middleware(['jwt.auth', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
});
