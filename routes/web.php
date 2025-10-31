<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/', function () {
    return redirect('/docs');
});

// Google OAuth (stateless)
Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('google.callback');

// Swagger UI and OpenAPI JSON
Route::get('/docs', function () {
    return view('swagger');
});
Route::get('/build/openapi.yml', function () {
    $build = public_path('build/openapi.yml');
    $path = file_exists($build) ? $build : public_path('openapi.yml');
    if (! file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/yaml',
    ]);
});
