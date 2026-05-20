<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\Auth\SocialAuthController;
use App\Http\Controllers\API\Auth\VerificationController;
use App\Http\Controllers\API\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

/**
 * Auth APIs.
 */
Route::group(['prefix' => 'auth'], function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('verify-otp', [VerificationController::class, 'verify_otp']);
    Route::post('social-login/{provider}', [SocialAuthController::class, 'social_login']);
    Route::post('forgot-password', [ResetPasswordController::class, 'forgot_password']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('resend-otp', [VerificationController::class, 'resend_otp']);
        Route::post('reset-password', [ResetPasswordController::class, 'reset_password']);
        Route::post('update-profile', [AuthController::class, 'update_profile']);
        Route::post('change-password', [AuthController::class, 'change_password']);
    });
});

/**
 * Payment APIs.
 */
Route::group(['prefix' => 'payment'], function () {
    Route::post('create', [PaymentController::class, 'createPayment'])->middleware('auth:api')->name('payment.create');
    Route::any('callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('payment-return', [PaymentController::class, 'paymentReturn'])->name('payment.return');
});
