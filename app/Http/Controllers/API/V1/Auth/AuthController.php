<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Http\Requests\API\UpdateUserRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\OTPService;
use App\Services\OTP;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function __construct(protected OTPService $OTPService) {}

    public function register(RegisterUserRequest $request)
    {
        try {
            $validated = $request->validated();
            // create user
            $user = User::create($validated);
            $data = ['token' => JWTAuth::fromUser($user), 'user' => $user];

            // sned otp if email is provided
            if (!empty($validated['email'])) {
                $status = $this->OTPService->send_email_otp($user);
                if (!$status['success']) {
                    return $this->errorResponse($status['message']);
                }
            }

            // send otp if phone is provided
            if (!empty($validated['phone'])) {
                $status = $this->OTPService->send_SMS_OTP($user);
                if (!$status['success']) {
                    return $this->errorResponse($status['message']);
                }
            }

            return $this->successResponse($data, 'User registered successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $credentials = [
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->unauthorizedResponse('Invalid credentials');
        }

        $user = auth()->user();

        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $validated['device_id'])
            ->first();

        if ($device) {
            $device->fcm_token = $validated['fcm_token'];
            $device->save();
        } else {
            UserDevice::create([
                'user_id'     => $user->id,
                'device_id'   => $validated['device_id'],
                'fcm_token'   => $validated['fcm_token'],
                'device_type' => $validated['device_type'],
            ]);
        }
        $is_verified = false;
        if ($user->registered_by === 'email' && !is_null($user->email_verified_at)) {
            $is_verified = true;
        }

        if (!$is_verified) {
            return $this->errorResponse(['is_verified' => false, 'user_id' => $user->id], 'User is not verified', 401);
        }

        $data = ['token' => $token, 'user' => $user];

        return $this->successResponse($data, 'User logged in successfully');
    }



    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse(null, 'Logged out successfully');
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


    public function update_profile(UpdateUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = auth()->user();

            // Check if trying to update both email and phone at once
            if (isset($validated['email']) && isset($validated['phone'])) {
                return $this->validationerrorResponse(['Cannot update both email and phone at the same time']);
            }

            // Handle email update with verification
            if (isset($validated['email']) && $validated['email'] !== $user->email) {
                $user->email = $validated['email'];
                $user->email_verified_at = null;

                // Send OTP for new email
                $status = $this->OTPService->send_email_otp($user);
                if (!$status['success']) {
                    return $this->errorResponse($status['message']);
                }

                unset($validated['email']);
                $needsVerification = true;
            }

            // Handle phone update with verification
            if (isset($validated['phone']) && $validated['phone'] !== $user->phone) {
                $user->phone = $validated['phone'];
                $user->phone_verified_at = null;
                $user->registered_by = 'phone';

                // Send OTP for new phone
                $status = $this->OTPService->send_SMS_OTP($user);
                if (!$status['success']) {
                    return $this->errorResponse($status['message']);
                }

                unset($validated['phone']);
                $needsVerification = true;
            }

            // handle if first name or last name is provided
            if (isset($validated['first_name']) || isset($validated['last_name'])) {
                $user->first_name = $validated['first_name'] ?? $user->first_name;
                $user->last_name = $validated['last_name'] ?? $user->last_name;
            }

            // Update remaining fields
            $user->fill($validated);
            $user->save();

            $message = isset($needsVerification)
                ? 'Profile updated successfully. Please verify your new contact information'
                : 'Profile updated successfully';

            $data = [
                'user' => $user->fresh(),
                'needs_verification' => isset($needsVerification)
            ];

            return $this->successResponse($data, $message);
        } catch (\Exception $e) {
            return $this->errorResponse('Update failed: ' . $e->getMessage());
        }
    }

    public function change_password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Hash::check($validated['current_password'], $user->password)) {
            return $this->validationerrorResponse(['current_password' => ['Current password is incorrect']]);
        }

        $user->password = bcrypt($validated['password']);
        $user->save();

        return $this->successResponse(null, 'Password changed successfully');
    }
}
