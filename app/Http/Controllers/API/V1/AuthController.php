<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        // check if either email or phone is provided
        if (empty($validated['email']) && empty($validated['phone'])) {
            return $this->respondValidationError('email or phone must be provided');
        }
        // check if both email and phone are provided
        if (!empty($validated['email']) && !empty($validated['phone'])) {
            return $this->respondValidationError('email and phone cannot be provided together');
        }

        $user = User::create($validated);
        $data = ['token' => JWTAuth::fromUser($user), 'user' => $user];

        return $this->respondSuccess($data, 'User registered successfully', 201);
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
            return $this->respondValidationError('Email or phone must be provided');
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->respondUnauthorized('Invalid credentials');
        }

        $user = auth()->user();
        $data = ['token' => $token,'user' => $user];

        return $this->respondSuccess($data, 'User logged in successfully');
    }



    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->respondSuccess(null, 'logged out successfully');
    }

    public function refresh()
    {
        $data['token'] = JWTAuth::refresh(JWTAuth::getToken());
        $data['user'] = auth()->user();
        return $this->respondSuccess($data);
    }


    public function me()
    {
        return $this->respondSuccess(auth()->user());
    }
}
