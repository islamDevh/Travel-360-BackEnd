<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\ChangePasswordRequest;
use App\Http\Requests\API\ForgotPasswordRequest;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Http\Requests\API\VerifyOtpRequest;
use App\Services\AuthService;

class AuthController extends BaseController
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Register a new user.
     */
    public function register(RegisterUserRequest $request)
    {
        $data = $this->authService->register($request->validated());
        return $this->successResponse($data);
    }

    /**
     * Authenticate a user and return a JWT token.
     */
    public function login(LoginUserRequest $request)
    {
        $data = $this->authService->login($request->validated());
        return $this->successResponse($data);
    }

    /**
     * Invalidate the current user's token.
     */
    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse(null);
    }

    /**
     * Refresh the current JWT token and return a new one.
     */
    public function refresh()
    {
        $data = $this->authService->refresh();
        return $this->successResponse($data);
    }

    /**
     * Return the authenticated user's profile data.
     */
    public function me()
    {
        $data = $this->authService->me();
        return $this->successResponse($data);
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $this->authService->updateProfile($request->validated());
        return $this->successResponse($data);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $this->authService->changePassword($request->validated());
        return $this->successResponse($data);
    }

    /**
     * Verify the OTP submitted by the user.
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $this->authService->verifyOtp($request->validated());
        return $this->successResponse($data);
    }

    /**
     * Resend a verification OTP to an unverified user.
     */
    public function resendOtp()
    {
        $this->authService->resendOtp();
        return $this->successResponse(null);
    }

    /**
     * Send a password reset OTP to the user.
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->validated());
        return $this->successResponse(null);
    }

    /**
     * Reset the user's password using a verified OTP.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());
        return $this->successResponse(null);
    }
}
