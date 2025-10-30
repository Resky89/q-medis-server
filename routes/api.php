<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\ResponseFormatter;

Route::get('/', function () {
    return ResponseFormatter::success([
        'service' => 'Q Medis API',
        'laravel' => app()->version(),
        'php' => PHP_VERSION,
        'time' => now()->toDateTimeString(),
    ], 'API is up');
});

// Modular API routes
require __DIR__.'/api/auth.php';
require __DIR__.'/api/display.php';
require __DIR__.'/api/lokets.php';
require __DIR__.'/api/antrians.php';
require __DIR__.'/api/users.php';

