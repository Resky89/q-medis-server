<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DisplayController;

Route::prefix('display')->group(function () {
    Route::get('lokets', [DisplayController::class, 'lokets']);
    Route::get('lokets/{loket}', [DisplayController::class, 'show']);
});
