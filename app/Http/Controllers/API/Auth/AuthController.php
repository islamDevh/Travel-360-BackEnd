<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Services\AuthService;

class AuthController extends BaseController
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $this->authService->register($request->validated());
        return $this->successResponse($data);
    }

    public function login(LoginUserRequest $request)
    {
        $data = $this->authService->login($request->validated());
        return $this->successResponse($data);
    }

    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse(null);
    }

    public function refresh()
    {
        $data = $this->authService->refresh();
        return $this->successResponse($data);
    }

    public function me()
    {
        $data = $this->authService->me();
        return $this->successResponse($data);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $this->authService->updateProfile($request->validated());
        return $this->successResponse($data);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authService->changePassword($request->validated());
        return $this->successResponse(null);
    }
}
