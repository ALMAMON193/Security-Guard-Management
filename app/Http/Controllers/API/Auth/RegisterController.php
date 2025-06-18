<?php

namespace App\Http\Controllers\API\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    use ResponseTrait;
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:business_owner,security_guard',
            'agree_terms' => 'required|boolean',
            'phone' => 'required|string|max:15',
            'passport_number' => 'required|string|max:20',
            'registration_code' => 'required|string|max:20',
        ]);
        try {
            $otp = random_int(1000, 9999);
            $otpExpiresAt = Carbon::now()->addMinutes(60);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
                'role' => $request->role,
                'phone' => $request->phone,
                'passport_number' => $request->passport_number,
                'registration_code' => $request->registration_code,
                'agree_terms' => $request->agree_terms,
                'is_verified' => false,
                'is_compliance' => false,
                'status' => 'pending',
            ]);
            // Send OTP email
            Mail::to($user->email)->send(new OtpMail($otp, $user, 'Verify Your Email Address'));
            return $this->sendResponse($user, 'User registered successfully. Please check your email to verify your account. OTP: ' . $otp);
        } catch (Exception $e) {
            Log::error('Register Error', (array)$e->getMessage());
            return $this->sendError('Something went wrong. Please try again later.'.$e->getMessage(), 500); // Ensure 500 is integer
        }
    }
    public function VerifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:4',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            // Check if email has already been verified
            if (!empty($user->email_verified_at)) {
                $user->is_verified = true;
                return $this->sendResponse($user, 'Email already verified.', 409); // Ensure 409 is integer

            }

            // Check if OTP code is valid
            if ((string)$user->otp !== (string)$request->input('otp')) {
                return $this->sendError('Invalid OTP code', 422); // Ensure 422 is integer
            }

            // Check if OTP has expired
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return $this->sendError('OTP has expired. Please request a new OTP.', 422); // Ensure 422 is integer
            }

            $token = $user->createToken('YourAppName')->plainTextToken;
            // Verify the email
            $user->email_verified_at = now();
            $user->is_verified = true;
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();
            return $this->sendResponse(
                data: [
                    'token_type' => 'bearer',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_verified' => $user->is_verified,
                        'role' => $user->role,
                        'is_compliance'=> $user->is_compliance,
                        'status' => $user->status,
                    ]
                ],
                message: 'Login successful. Your account is pending admin verification to access all features.',
                code: 200
            );
        } catch (Exception $e) {
            Log::error('VerifyEmail Error', (array)$e->getMessage());
            return $this->sendError('Something went wrong. Please try again later.' . $e->getMessage(), 500); // Ensure 500 is integer
        }
    }

    public function ResendOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        try {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return  $this->sendError('User not found.', 404);
            }

            if ($user->email_verified_at) {
                return  $this->sendError('Email already verified.', 409);
            }

            $newOtp               = random_int(1000, 9999);
            $otpExpiresAt         = Carbon::now()->addMinutes(60);
            $user->otp            = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();
            Mail::to($user->email)->send(new OtpMail($newOtp, $user, 'Verify Your Email Address'));

            return  $this->sendResponse($user, 'OTP resent successfully. Please check your email to verify your account.');
        } catch (Exception $e) {
            Log::error('ResendOtp Error', (array)$e->getMessage());
            return  $this->sendError('Something went wrong. Please try again later.', 500); // Ensure 500 is integer
        }
    }
}
