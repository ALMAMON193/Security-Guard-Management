<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthApiController;
use App\Http\Controllers\API\BusinessOwner\V1\CompanyProfileController;
use App\Http\Controllers\API\BusinessOwner\V1\ComplianceSetupController;


//auth route
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthApiController::class, 'loginApi']);
    Route::post('register', [AuthApiController::class, 'registerApi']);
    Route::post('verify-email', [AuthApiController::class, 'verifyEmailApi']);
    Route::post('forgot-password', [AuthApiController::class, 'forgotPasswordApi']);
    Route::post('reset-password', [AuthApiController::class, 'resetPasswordApi']);
    Route::post('resend-otp', [AuthApiController::class, 'resendOtpApi']);
    Route::post('verify-otp', [AuthApiController::class, 'verifyOtpApi']);
});


Route::group(['middleware' => 'auth:sanctum'], static function () {

    Route::post('/logout', [AuthApiController::class, 'logoutApi']);
    //compliance setup controller
    Route::controller(ComplianceSetupController::class)->prefix('business-owner')->group(function () {
        Route::post('/v1/compliance-setup', 'CreateComplianceSetup');
    });
     //Company Profile setup controller
    Route::controller(CompanyProfileController::class)->prefix('business-owner')->group(function () {
        Route::post('/v1/company-profile', 'CreateCompanyProfile');
    });
});
