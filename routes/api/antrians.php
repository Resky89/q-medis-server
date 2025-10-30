<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AntrianController;

Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('antrians', AntrianController::class)->only(['index','show','store','update']);
});
