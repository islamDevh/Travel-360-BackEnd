<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Hash;
use App\Services\OTPService;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(protected OTPService $OTPService) {}

    public function register(array $validated)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'first_name'    => $validated['first_name'],
                'last_name'     => $validated['last_name'],
                'email'         => $validated['email'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'password'      => bcrypt($validated['password']),
                'registered_by' => isset($validated['email']) ? 'email' : 'phone',
            ]);

            $user->devices()->create([
                'device_id'   => $validated['device_id'],
                'fcm_token'   => $validated['fcm_token'],
                'device_type' => $validated['device_type'],
            ]);

            DB::commit();

            $otpResult = $this->sendOTP($user, $validated);

            $message = 'Registered successfully, please verify your account with the sent OTP';

            if (!$otpResult['success']) {
                $message = 'Registered successfully, but OTP could not be sent: ' . $otpResult['message'];
            }

            $data = ['token' => JWTAuth::fromUser($user), 'user' => $user];
            return ['success' => true, 'data' => $data, 'message' => $message];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }


    public function sendOTP(User $user, array $validated)
    {
        if (!empty($validated['email'])) {
            $result = $this->OTPService->send_email_otp($user);
            if (!$result['success']) return $result;
        }

        if (!empty($validated['phone'])) {
            $result = $this->OTPService->send_SMS_OTP($user);
            if (!$result['success']) return $result;
        }

        return ['success' => true];
    }


    public function login(array $validated)
    {
        $credentials = [
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $user = auth()->user();

        // create or update device info
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $validated['device_id'])
            ->first();

        if ($device) {
            $device->update(['fcm_token' => $validated['fcm_token']]);
        } else {
            UserDevice::create([
                'user_id'     => $user->id,
                'device_id'   => $validated['device_id'],
                'fcm_token'   => $validated['fcm_token'],
                'device_type' => $validated['device_type'],
            ]);
        }

        // Check verification
        $is_verified = $user->registered_by === 'email'
            ? !is_null($user->email_verified_at)
            : !is_null($user->phone_verified_at);

        if (!$is_verified) {
            return [
                'success' => false,
                'message' => 'User is not verified',
                'data'    => ['is_verified' => false, 'user_id' => $user->id],
            ];
        }

        $data = ['token' => $token, 'user' => $user];

        return ['success' => true, 'data'    => $data, 'message' => 'User logged in successfully'];
    }


    public function updateProfile(User $user, array $validated)
    {
        try {
            $needsVerification = false;

            // Handle email update with verification
            if (isset($validated['email']) && $validated['email'] !== $user->email) {
                $user->email             = $validated['email'];
                $user->email_verified_at = null;

                $status = $this->OTPService->send_email_otp($user);
                if (!$status['success']) {
                    return ['success' => false, 'message' => $status['message']];
                }

                unset($validated['email']);
                $needsVerification = true;
            }

            // Handle phone update with verification
            if (isset($validated['phone']) && $validated['phone'] !== $user->phone) {
                $user->phone             = $validated['phone'];
                $user->phone_verified_at = null;
                $user->registered_by     = 'phone';

                $status = $this->OTPService->send_SMS_OTP($user);
                if (!$status['success']) {
                    return ['success' => false, 'message' => $status['message']];
                }

                unset($validated['phone']);
                // $needsVerification = true;
            }

            $user->first_name = $validated['first_name'] ?? $user->first_name;
            $user->last_name  = $validated['last_name']  ?? $user->last_name;
            
            $user->fill($validated);

            // update only if there are changes
            if ($user->isDirty()) {
                $user->save();
            }

            return [
                'success' => true,
                'data'    => [
                    'user'               => $user->fresh(),
                    'needs_verification' => $needsVerification
                ],
                'message' => $needsVerification
                    ? 'Profile updated successfully. Please verify your new contact information'
                    :  'Profile updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }



    public function changePassword(User $user, array $validated)
    {
        if (!Hash::check($validated['current_password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect',
                'status'  => 422,
            ];
        }

        $user->update(['password' => bcrypt($validated['password'])]);

        return [
            'success' => true,
            'message' => 'Password changed successfully',
        ];
    }
}
