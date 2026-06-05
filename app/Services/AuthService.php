<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Device;
use App\Supports\OtpSupport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Create a new user, register their device, and send a verification OTP.
     */
    public function register(array $data)
    {
        DB::beginTransaction();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'full_name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'registered_by' => $data['registered_by'],
        ]);

        $user->devices()->create([
            'fcm_token' => $data['fcm_token'],
            'device_type' => $data['device_type'],
        ]);

        DB::commit();

        // send otp 
        app(OtpSupport::class)->send($data['registered_by'], $user->email);

        return [
            'token' => JWTAuth::fromUser($user),
            'user' => new UserResource($user),
        ];
    }

    /**
     * Validate credentials, upsert the user's device, and return a JWT token.
     */
    public function login(array $data)
    {
        if (!$token = JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            abort(401, 'Invalid email or password.');
        }

        $user = auth()->user();

        $device = Device::where('user_id', $user->id)
            ->where('device_id', $data['device_id'])
            ->first();

        if ($device) {
            $device->update(['fcm_token' => $data['fcm_token']]);
        } else {
            Device::create([
                'user_id' => $user->id,
                'device_id' => $data['device_id'],
                'fcm_token' => $data['fcm_token'],
                'type' => $data['type'],
            ]);
        }

        return [
            'token' => $token,
            'user' => new UserResource($user),
        ];
    }

    /**
     * Invalidate the current JWT token.
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Return the authenticated user's data.
     */
    public function me()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Refresh the current JWT token and return a new one.
     */
    public function refresh()
    {
        return [
            'token' => JWTAuth::refresh(JWTAuth::getToken()),
            'user' => new UserResource(auth()->user()),
        ];
    }

    /**
     * Verify the OTP for a given user and mark their contact as verified.
     */
    public function verifyOtp(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        $identifier = $user->registered_by === 'email' ? $user->email : $user->phone;

        app(OtpSupport::class)->validate($identifier, $data['otp']);

        if ($user->registered_by === 'email') {
            $user->email_verified_at = now();
        } else {
            $user->phone_verified_at = now();
        }

        $user->save();

        return [
            'token' => JWTAuth::fromUser($user),
            'user' => new UserResource($user),
        ];
    }

    /**
     * Resend a verification OTP to the authenticated user.
     */
    public function resendOtp()
    {
        $user = auth()->user();
        app(OtpSupport::class)->send($user->registered_by, $user->email);
        return true;
    }

    /**
     * Look up the user by email or phone and send a password reset OTP.
     */
    public function forgotPassword(array $data)
    {
        if ($data['registered_by'] === 'email') {
            $user = User::where('email', $data['email'])->firstOrFail();
            app(OtpSupport::class)->send('email', $user->email);
        } else {
            $user = User::where('phone', $data['phone'])->firstOrFail();
            app(OtpSupport::class)->send('sms', $user->phone);
        }
    }

    /**
     * Set a new password for the authenticated user.
     */
    public function resetPassword(array $data)
    {
        $user = auth()->user();
        $user->password = $data['password'];
        $user->save();
    }

    /**
     * Update the authenticated user's profile fields and avatar.
     */
    public function updateProfile(array $data)
    {
        $user = auth()->user();

        $user->update($data);

        if (isset($data['image'])) {
            $user->addMedia($data['image'])->toMediaCollection('avatar');
        }

        return true;
    }

    /**
     * Verify the current password then update it to the new one.
     */
    public function changePassword(array $data)
    {
        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            abort(400, 'Current password is incorrect.');
        }

        $user->update(['password' => $data['password']]);
        return true;
    }
}
