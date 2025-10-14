<?php

namespace App\Exceptions;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiHandler extends BaseController
{
    public function __invoke(Throwable $e, Request $request)
    {
        if ($request->is('api/*') || $request->expectsJson()) {

            if ($e instanceof ModelNotFoundException) {
                return $this->errorResponse('Resource not found', 404);
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->errorResponse('API not found', 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse('Method not allowed', 405);
            }

            if ($e instanceof AuthenticationException) {
                return $this->errorResponse('Unauthenticated', 401);
            }

            if ($e instanceof AuthorizationException) {
                return $this->errorResponse('Forbidden', 403);
            }

            if ($e instanceof ThrottleRequestsException) {
                return $this->errorResponse('Too many requests', 429);
            }

            if ($e instanceof ValidationException) {
                return $this->validationerrorResponse($e->errors(), 'Validation failed');
            }

            // Show actual message in dev, generic in production
            $message = config('app.debug') ? 'Server error: ' . $e->getMessage() : 'Server error';
            $trace = config('app.debug') ? $e->getTrace() : null;

            return $this->errorResponse($trace, $message, 500);
        }

        return null;
    }
}
