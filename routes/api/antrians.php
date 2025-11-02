<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AntrianController;

Route::middleware('jwt.auth')->group(function () {
    // Read-only list and detail for any authenticated user (including petugas)
    Route::get('antrians', [AntrianController::class, 'index']);
    Route::get('antrians/{antrian}', [AntrianController::class, 'show']);

    // Update status allowed for admin or petugas
    Route::match(['put', 'patch'], 'antrians/{antrian}', [AntrianController::class, 'update'])
        ->middleware('role:admin,petugas');
});
