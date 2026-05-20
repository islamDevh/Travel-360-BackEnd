<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\OTPService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(protected OTPService $OTPService)
    {
    }

    public function register(array $data): array
    {
        DB::beginTransaction();

        $user = User::create([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'full_name'     => $data['first_name'] . ' ' . $data['last_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'password'      => $data['password'],
            'registered_by' => $data['registered_by'],
        ]);

        $user->devices()->create([
            'device_id'   => $data['device_id'],
            'fcm_token'   => $data['fcm_token'],
            'device_type' => $data['device_type'],
        ]);

        DB::commit();

        if ($data['registered_by'] === 'email') {
            $this->OTPService->send_email_otp($user);
        } else {
            $this->OTPService->send_SMS_OTP();
        }

        return [
            'user'  => new UserResource($user),
            'token' => JWTAuth::fromUser($user),
        ];
    }

    public function login(array $data): array
    {
        if (!$token = JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            abort(401, 'Invalid email or password.');
        }

        $user = auth()->user();

        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $data['device_id'])
            ->first();

        if ($device) {
            $device->update(['fcm_token' => $data['fcm_token']]);
        } else {
            UserDevice::create([
                'user_id'     => $user->id,
                'device_id'   => $data['device_id'],
                'fcm_token'   => $data['fcm_token'],
                'device_type' => $data['device_type'],
            ]);
        }

        return [
            'token' => $token,
            'user'  => new UserResource($user),
        ];
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function me(): UserResource
    {
        return new UserResource(auth()->user());
    }

    public function refresh(): array
    {
        return [
            'token' => JWTAuth::refresh(JWTAuth::getToken()),
            'user'  => new UserResource(auth()->user()),
        ];
    }

    public function verifyOtp(array $data): array
    {
        $user = User::findOrFail($data['user_id']);

        $this->OTPService->verify_OTP($data['otp'], $user);

        return [
            'token' => JWTAuth::fromUser($user),
            'user'  => new UserResource($user),
        ];
    }

    public function resendOtp(): void
    {
        $user = auth()->user();

        if ($user->registered_by === 'email') {
            $this->OTPService->send_email_otp($user);
        } else {
            $this->OTPService->send_SMS_OTP();
        }
    }

    public function forgotPassword(array $data): void
    {
        if ($data['registered_by'] === 'email') {
            $user = User::where('email', $data['email'])->firstOrFail();
            $this->OTPService->send_email_otp($user);
        } else {
            $user = User::where('phone', $data['phone'])->firstOrFail();
            $this->OTPService->send_SMS_OTP();
        }
    }

    public function resetPassword(array $data): void
    {
        $user           = auth()->user();
        $user->password = $data['password'];
        $user->save();
    }

    public function updateProfile(array $data): UserResource
    {
        $user = auth()->user();

        $user->update($data);

        if (isset($data['image'])) {
            $user->addMedia($data['image'])->toMediaCollection('avatar');
        }

        return new UserResource($user->fresh());
    }

    public function changePassword(array $data): void
    {
        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            abort(400, 'Current password is incorrect.');
        }

        $user->update(['password' => $data['password']]);
    }
}
