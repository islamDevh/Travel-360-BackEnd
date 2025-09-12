<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);

        Route::post('resend_otp', [VerificationController::class, 'resend_otp']);
        Route::post('verify_otp', [VerificationController::class, 'verify_otp']);

        Route::post('send_otp_forgot_password', [ResetPasswordController::class, 'send_otp_forgot_password']);
        Route::post('reset_password_by_otp', [ResetPasswordController::class, 'reset_password_by_otp']);
    });
});
