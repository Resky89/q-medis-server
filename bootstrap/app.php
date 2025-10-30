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
        // JSON formatting for common exceptions
        $json = function (string $message, int $status, $errors = null) {
            if ($errors === null) {
                $errors = (object) [];
            }
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
            ], $status);
        };

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('validation error', 422, $e->errors());
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('unauthenticated', 401);
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('forbidden', 403);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('not found', 404);
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('not found', 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('method not allowed', 405);
        });

        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            return $json('too many requests', 429);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            $status = $e->getStatusCode();
            $message = $e->getMessage() ?: 'error';
            return $json($message, $status);
        });

        $exceptions->render(function (\Throwable $e, $request) use ($json) {
            if (! ($request->expectsJson() || $request->is('api/*'))) return null;
            $message = config('app.debug') ? ($e->getMessage() ?: 'server error') : 'server error';
            return $json($message, 500);
        });
    })->create();

