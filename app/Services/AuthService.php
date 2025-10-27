<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Hash;
use App\Services\OTPService;
use App\Traits\UploadFiles;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    use UploadFiles;
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
        // $is_verified = $user->registered_by === 'email' ? !is_null($user->email_verified_at) : !is_null($user->phone_verified_at);
        $is_verified = $user->email_verified_at ? true : false;

        if (!$is_verified) {
            return [
                'success' => false,
                'message' => 'User is not verified',
                'data'    => ['is_verified' => false, 'user_id' => $user->id],
            ];
        }

        return [
            'success' => true,
            'message' => 'User logged in successfully',
            'data'    => ['token' => $token, 'user' => $user],
        ];
    }


    public function updateProfile(User $user, array $validated)
    {
        try {
            $needsVerification = false;

            // --- EMAIL UPDATE (with verification) ---
            if (isset($validated['email']) && $validated['email'] !== $user->email) {
                $newEmail = $validated['email'];

                // assign it before sending OTP
                $user->email             = $newEmail;
                $user->email_verified_at = null;

                $status = $this->OTPService->send_email_otp($user);
                if (!$status['success']) {
                    return [
                        'success' => false,
                        'message' => $status['message'],
                    ];
                }

                // prevent update() from overwriting this logic again
                unset($validated['email']);
                $needsVerification = true;
            }

            // --- PHONE UPDATE (with verification) ---
            if (isset($validated['phone']) && $validated['phone'] !== $user->phone) {
                $newPhone = $validated['phone'];

                $user->phone             = $newPhone;
                $user->phone_verified_at = null;
                $user->registered_by     = 'phone';

                $status = $this->OTPService->send_SMS_OTP($user);
                if (!$status['success']) {
                    return [
                        'success' => false,
                        'message' => $status['message'],
                    ];
                }

                unset($validated['phone']);
                // uncomment if you want phone changes to also trigger verification notice
                // $needsVerification = true;
            }

            // --- PASSWORD UPDATE (hash before saving) ---
            if (!empty($validated['password'])) {
                $validated['password'] = $validated['password'];
            }
            // update image if provided
            if (isset($validated['image'])) {
                // remove old image
                $this->removeFile(userStoragePath . $user->image);

                // upload new image
                $image_name         = $this->uploadFile($validated['image'], userStoragePath);
                $validated['image'] = $image_name;
            }

            $user->update($validated);

            return [
                'success' => true,
                'data' => [
                    'user' => $user->fresh(),
                    'needs_verification' => $needsVerification,
                ],
                'message' => $needsVerification
                    ? 'Profile updated successfully. Please verify your new contact information.'
                    : 'Profile updated successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Profile update failed: ' . $e->getMessage(),
            ];
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
