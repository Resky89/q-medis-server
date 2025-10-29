<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        if (class_exists('PHPOpenSourceSaver\\JWTAuth\\Http\\Middleware\\Authenticate') && class_exists('PHPOpenSourceSaver\\JWTAuth\\Http\\Middleware\\RefreshToken')) {
            $middleware->alias([
                'jwt.auth' => 'PHPOpenSourceSaver\\JWTAuth\\Http\\Middleware\\Authenticate',
                'jwt.refresh' => 'PHPOpenSourceSaver\\JWTAuth\\Http\\Middleware\\RefreshToken',
            ]);
        }
        if (class_exists('App\\Http\\Middleware\\JwtMiddleware')) {
            $middleware->alias(['jwt.verify' => 'App\\Http\\Middleware\\JwtMiddleware']);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
