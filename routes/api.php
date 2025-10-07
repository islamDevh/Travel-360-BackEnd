<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Auth\ResetPasswordController;
use App\Http\Controllers\API\V1\Auth\SocialAuthController;
use App\Http\Controllers\API\V1\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('resend_otp', [VerificationController::class, 'resend_otp']);
        Route::post('forgot_password', [ResetPasswordController::class, 'forgot_password']);
        Route::post('reset_password', [ResetPasswordController::class, 'reset_password']);
        Route::put('update_profile', [AuthController::class, 'update_profile']);
        Route::post('change_password', [AuthController::class, 'change_password']);
    });
    Route::post('verify_otp', [VerificationController::class, 'verify_otp']);

    Route::post('social_login', [SocialAuthController::class, 'social_login']);

});
