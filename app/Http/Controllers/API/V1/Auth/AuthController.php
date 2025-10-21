<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Http\Requests\API\UpdateUserRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function __construct(protected AuthService $authService) {}

    public function register(RegisterUserRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());

            if (!$result['success']) {
                return $this->errorResponse($result['message']);
            }

            return $this->successResponse($result['data'], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(LoginUserRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result['success']) {
            return $this->errorResponse($result['message']);
        }

        return $this->successResponse($result['data'], $result['message']);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse(null, 'Logged out successfully');
    }


    public function refresh()
    {
        $data['token'] = JWTAuth::refresh(JWTAuth::getToken());
        $data['user']  = auth()->user();
        return $this->successResponse($data);
    }

    public function me()
    {
        return $this->successResponse(auth()->user());
    }

    public function update_profile(UpdateUserRequest $request)
    {
        $result = $this->authService->updateProfile(auth()->user(), $request->validated());

        if (!$result['success']) {
            return $this->errorResponse(null, $result['message'], $result['status']);
        }

        return $this->successResponse($result['data'], $result['message']);
    }

    public function change_password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->changePassword(auth()->user(), $validated);

        if (!$result['success']) {
            return $this->errorResponse(null, $result['message'], $result['status']);
        }

        return $this->successResponse(null, $result['message']);
    }
}
