<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function successResponse($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorResponse($errors = [], $message = 'Something went wrong', $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors ?: null,
        ], $code);
    }

    public function notFoundResponse($message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse([], $message, 404);
    }

    public function validationerrorResponse($errors, $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($errors, $message, 422);
    }


    public function unauthorizedResponse($message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse([], $message, 401);
    }

    public function PaginationResponse($paginator, $message = 'Data retrieved successfully', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $code,
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
