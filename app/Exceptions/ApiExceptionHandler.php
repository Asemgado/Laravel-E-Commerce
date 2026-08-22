<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionHandler
{
    public function render(\Throwable $exception, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        return match (true) {
            $exception instanceof AuthenticationException => response()->json([
                'message' => $exception->getMessage() ?? 'Unauthenticated.',
            ], 401),

            $exception instanceof AuthorizationException => response()->json([
                'message' => $exception->getMessage() ?? 'Forbidden.',
            ], 403),

            $exception instanceof NotFoundHttpException => response()->json([
                'message' => $exception->getMessage() ?? 'Resource not found.',
            ], 404),
            $exception instanceof ValidationException => response()->json([
                'message' => $exception->getMessage() ?? 'The given data was invalid.',
                'errors' => $exception->errors(),
            ], 422),

            $exception instanceof InvalidArgumentException => response()->json([
                'message' => $exception->getMessage() ?? 'Invalid Argument Exeption.',
            ], 422),

            default => response()->json([
                'message' => $exception->getMessage() ?? 'An unexpected server error occurred.',
            ], 500),
        };
    }
}