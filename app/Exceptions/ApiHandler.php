<?php

namespace App\Exceptions;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiHandler extends BaseController
{
    public function __invoke(Throwable $e, Request $request)
    {
        if ($request->is('api/*') || $request->expectsJson()) {

            if ($e instanceof ModelNotFoundException) return $this->errorResponse('Resource not found', 404);
            if ($e instanceof NotFoundHttpException) return $this->errorResponse('API not found', 404);
            if ($e instanceof MethodNotAllowedHttpException) return $this->errorResponse('Method not allowed', 405);
            if ($e instanceof AuthenticationException) return $this->errorResponse('Unauthenticated', 401);
            if ($e instanceof AuthorizationException) return $this->errorResponse('Forbidden', 403);
            if ($e instanceof ThrottleRequestsException) return $this->errorResponse('Too many requests', 429);
            if ($e instanceof ValidationException) return $this->validationerrorResponse($e->errors(), 'Validation failed');
            if ($e instanceof MissingAttributeException) return $this->errorResponse($e->getMessage(), 400);

            // JWT/Auth
            if ($e instanceof TokenExpiredException) return $this->errorResponse('Token expired', 401);
            if ($e instanceof TokenInvalidException) return $this->errorResponse('Token invalid', 401);
            if ($e instanceof JWTException) return $this->errorResponse('Token not provided or invalid', 401);

            // Database & Query
            if ($e instanceof QueryException) return $this->errorResponse('Database query error', 500);

              // PHP/Method errors
            if ($e instanceof \BadMethodCallException) return $this->errorResponse('Bad method call: ' . $e->getMessage(), 400);
            if ($e instanceof \ErrorException) return $this->errorResponse($e->getMessage(), 400);


            return $this->errorResponse(config('app.debug') ? $e->getMessage() : 'Server error', 500);
        }
    }
}
