<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function respondSuccess($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }


    protected function respondError($errors = [], $message = 'Something went wrong', $code = 500): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'errors' => $errors ?: null,
        ], $code);
    }

    protected function respondNotFound($message = 'Resource not found'): JsonResponse
    {
        return $this->respondError([], $message, 404);
    }

    protected function respondUnauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    protected function respondForbidden($message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'status' => 403,
            'message' => $message,
        ], 403);
    }

    protected function respondValidationError($errors, $message = 'Validation failed'): JsonResponse
    {
        return $this->respondError($errors, $message, 422);
    }

    protected function respondWithPagination($paginator, $message = 'Data retrieved successfully', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], $code);
    }
}
