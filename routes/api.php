<?php

use App\Http\Controllers\Api\V1\Auth\EmailOtpController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\API\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);


    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('/email/otp/resend', [AuthController::class, 'resendEmailOtp']);
        Route::post('/email/otp/verify', [AuthController::class, 'verifyOtp']);
        Route::post('/password/forgot/send-otp', [AuthController::class, 'sendOtpForgotPassword']);
        Route::post('/password/forgot/reset', [AuthController::class, 'resetPasswordWithOtp']);
    });
});
