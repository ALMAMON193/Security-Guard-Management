<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\V1\CompanyProfileController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\V1\ComplianceSetupController;

//register
Route::post('register', [RegisterController::class, 'register']);
Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
//login
Route::post('login', [LoginController::class, 'login']);
//forgot password
Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP']);
Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);

Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);


    //compliance setup controller
    Route::controller(ComplianceSetupController::class)->group(function () {
        Route::post('/v1/create-compliance-setup', 'CreateComplianceSetup');
    });

    //setup Componey Profile
    Route::controller(CompanyProfileController::class)->group(function () {
        Route::post('/v1/create-compliance-setup', 'CreateComplianceSetup');
    });

});
