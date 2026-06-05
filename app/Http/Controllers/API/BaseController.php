<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function successResponse($data = null, $message = 'Success operation', $code = 200)
    {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorResponse($message = 'Something went wrong', $code = 500)
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
        ], $code);
    }

    public function validationErrorResponse(array $errors, $message = 'Validation failed', $code = 422)
    {
        $formatted = [];

        foreach ($errors as $field => $messages) {
            $formatted[$field] = $messages[0];
        }

        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => $formatted,
        ], $code);
    }
}
