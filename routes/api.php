<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthApiController;
use App\Http\Controllers\API\BusinessOwner\V1\GuardApiController;
use App\Http\Controllers\API\SecurityGuard\V1\ShiftApiController;
use App\Http\Controllers\API\SecurityGuard\V1\ComplianceApiController;
use App\Http\Controllers\API\BusinessOwner\V1\CompanyProfileController;
use App\Http\Controllers\API\BusinessOwner\V1\ComplianceSetupController;

// Auth routes (no middleware)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthApiController::class, 'loginApi']);
    Route::post('register', [AuthApiController::class, 'registerApi']);
    Route::post('verify-email', [AuthApiController::class, 'verifyEmailApi']);
    Route::post('forgot-password', [AuthApiController::class, 'forgotPasswordApi']);
    Route::post('reset-password', [AuthApiController::class, 'resetPasswordApi']);
    Route::post('resend-otp', [AuthApiController::class, 'resendOtpApi']);
    Route::post('verify-otp', [AuthApiController::class, 'verifyOtpApi']);
});

// Authenticated routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthApiController::class, 'logoutApi']);

    // Business Owner routes
    Route::group(['middleware' => 'business_owner', 'prefix' => 'business-owner/v1'], function () {
        // Compliance setup controller
        Route::controller(ComplianceSetupController::class)->group(function () {
            Route::post('compliance-setup', 'createComplianceSetup');
        });

        // Company Profile setup controller
        Route::controller(CompanyProfileController::class)->group(function () {
            Route::post('company-profile', 'createCompanyProfile');
        });

        // Guard management controller
        Route::controller(GuardApiController::class)->group(function () {
            Route::post('guard-create', 'createGuard');
        });
    });

    // Security Guard routes
    Route::group([ 'middleware' => 'security_guard', 'prefix' => 'security-guard/v1'], function () {
        // Compliance controller
        Route::controller(ComplianceApiController::class)->group(function () {
            Route::post('compliance-create', 'createCompliance');
        });

        // Shift management controller
        Route::controller(ShiftApiController::class)->group(function () {
            Route::post('shift-create', 'createShift');
            Route::get('today-shift', 'todayShift');
            Route::delete('shift-delete/{id}', 'todayShiftDelete');
        });
    });
});
