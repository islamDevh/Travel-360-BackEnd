<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Models\User;
use App\Services\API\OTP;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function register(RegisterUserRequest $request)
    {
        try {
            $validated = $request->validated();

            // check if either email or phone is not provided
            if (empty($validated['email']) && empty($validated['phone'])) {
                return $this->respondValidationError('email or phone must be provided');
            }

            // check if user register with email and phone
            if (!empty($validated['email']) && !empty($validated['phone'])) {
                return $this->respondValidationError('cannot not registered with email and phone together');
            }

            // create user
            $user = User::create($validated);
            $data = ['token' => JWTAuth::fromUser($user), 'user' => $user];

            // sned otp if email is provided
            if (!empty($validated['email'])) {
                (new OTP)->sendEmailOtp($user);
                return $this->respondSuccess($data, 'User registered and sent otp successfully.', 201);
            }

            return $this->respondSuccess($data, 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->respondError('Registration failed: ' . $e->getMessage());
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
            return $this->respondValidationError('Email or phone must be provided');
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->respondUnauthorized('Invalid credentials');
        }

        $user = auth()->user();
        $data = ['token' => $token, 'user' => $user];
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

    public function resendEmailOtp()
    {
        $user = auth()->user();
        if (empty($user->email)) {
            return $this->respondValidationError('User does not have an email to send OTP');
        }
        (new OTP)->sendEmailOtp($user);
        return $this->respondSuccess(null, 'OTP resent successfully');
    }

    public function verifyOtp()
    {
        $result = (new OTP)->verifyOtp(request('otp'));

        if ($result['success']) {
            return $this->respondSuccess(null, $result['message']);
        }

        return $this->respondError([], $result['message']);
    }

    public function sendOtpForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email',]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->respondError(null, 'User not found');
        }
        
        (new OTP)->sendEmailOtp($user);
        return $this->respondSuccess(null, 'OTP sent successfully to your email');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->respondError(null, 'User not found');
        }

        // تحقق من OTP
        $otpRecord = User::where('id', $user->id)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return $this->respondError(null, 'Invalid OTP or OTP has expired');
        }

        $user->password = $request->new_password;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();


        return response()->json(['success' => true, 'message' => 'Password reset successfully']);
    }
}
