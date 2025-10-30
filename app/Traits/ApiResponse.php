<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        $default = [
            400 => 'bad request',
            401 => 'unauthorized',
            403 => 'forbidden',
            404 => 'not found',
            405 => 'method not allowed',
            422 => 'validation error',
            429 => 'too many requests',
            500 => 'server error',
        ];
        $msg = ($message === 'Error' || $message === '' || $message === null)
            ? ($default[$status] ?? 'error')
            : $message;
        if ($errors === null) {
            $errors = (object) [];
        }
        return response()->json([
            'status' => 'error',
            'message' => $msg,
            'errors' => $errors,
        ], $status);
    }
}

