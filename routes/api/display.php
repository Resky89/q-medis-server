<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DisplayController;
use App\Http\Controllers\Api\AntrianController;

Route::prefix('display')->group(function () {
    Route::get('lokets', [DisplayController::class, 'lokets']);
    Route::get('lokets/{loket}', [DisplayController::class, 'show']);
    Route::get('overview', [DisplayController::class, 'overview']);
    Route::post('antrians', [AntrianController::class, 'store'])->middleware('throttle:60,1');
});
