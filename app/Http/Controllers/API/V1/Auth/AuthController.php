<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Models\User;
use App\Services\API\OTPService;
use App\Services\API\OTP;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function __construct(protected OTPService $OTPService) {}

    public function register(RegisterUserRequest $request)
    {
        try {
            $validated = $request->validated();
            // check if either email or phone is not provided
            if (empty($validated['email']) && empty($validated['phone'])) {
                return $this->validationerrorResponse(['email or phone must be provided']);
            }
            // check if user register with email and phone
            if (!empty($validated['email']) && !empty($validated['phone'])) {
                return $this->validationerrorResponse(['only one of email or phone must be provided']);
            }

            // create user
            $user = User::create($validated);
            $data = ['token' => JWTAuth::fromUser($user), 'user' => $user];

            // sned otp if email is provided
            if (!empty($validated['email'])) {
                $status = $this->OTPService->send_email_otp($user);
                if (!$status['success']) {
                    return $this->errorResponse([], $status['message']);
                }
            }
            // send otp if phone is provided
            if (!empty($validated['phone'])) {
                $status = $this->OTPService->send_SMS_OTP($user);
                if (!$status['success']) {
                    return $this->errorResponse([], $status['message']);
                }
            }

            return $this->successResponse($data, 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $credentials = ['password' => $validated['password']];
        if (!empty($validated['email'])) {
            $credentials['email'] = $validated['email'];
        } elseif (!empty($validated['phone'])) {
            $credentials['phone'] = $validated['phone'];
        } else {
            return $this->validationerrorResponse(['Email or phone must be provided']);
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->unauthorizedResponse('Invalid credentials');
        }

        $user = auth()->user();
        $is_verified = false;
        if ($user->registered_by === 'email' && !is_null($user->email_verified_at)) {
            $is_verified = true;
        }
        if ($user->registered_by === 'phone' && !is_null($user->phone_verified_at)) {
            $is_verified = true;
        }

        if (!$is_verified) {
            return $this->errorResponse(['is_verified' => false,'user_id' => $user->id], 'User is not verified', 401);
        }

        $data = ['token' => $token, 'user' => $user];

        return $this->successResponse($data, 'User logged in successfully');
    }



    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse(null, 'logged out successfully');
    }

    public function refresh()
    {
        $data['token'] = JWTAuth::refresh(JWTAuth::getToken());
        $data['user'] = auth()->user();
        return $this->successResponse($data);
    }


    public function me()
    {
        return $this->successResponse(auth()->user());
    }
}

