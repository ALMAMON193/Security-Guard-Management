<?php

namespace App\Http\Controllers\API\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    use ResponseTrait;

    public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $email = $request->input('email');
            $otp = random_int(1000, 9999);
            $user = User::where('email', $email)->first();

            if (!$user) {
               return $this->sendError('User account not found.', 404);
            }

            Mail::to($email)->send(new OtpMail($otp, $user, 'Reset Your Password'));

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(60),
            ]);

            return $this->sendResponse(
                data: ['email' => $email],
                message: 'An OTP has been sent to your email. Please check your inbox.',
                code: 200
            );

        } catch (Exception $e) {
            return $this->sendError('Failed to send OTP. Please try again.', 500, ['system_error' => $e->getMessage()]);
        }
    }

    public function VerifyOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
               return $this->sendError('User account not found.', 404);
            }

            if (Carbon::parse($user->otp_expires_at)->isPast()) {
               return $this->sendError('OTP has expired. Please request a new OTP.', 400);
            }

            if ($user->otp !== $request->otp) {
               return $this->sendError('Invalid OTP code. Please check and try again.', 422);
            }

            $token = Str::random(60);
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'reset_password_token' => $token,
                'reset_password_token_expire_at' => Carbon::now()->addHour(),
            ]);

            return $this->sendResponse('OTP verified successfully. You can now reset your password',200);

        } catch (Exception $e) {
          return $this->sendError('Failed to verify OTP. Please try again.', 500, ['system_error' => $e->getMessage()]);
        }
    }

    public function ResetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
               return $this->sendError('User account not found.', 404);
            }

            $tokenValid = $user->reset_password_token === $request->token &&
                $user->reset_password_token_expire_at >= Carbon::now();

            if (!$tokenValid) {
              return $this->sendError('Invalid or expired token. Please request a new OTP.', 422);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'reset_password_token' => null,
                'reset_password_token_expire_at' => null,
            ]);

            return $this->sendResponse(
                data: ['email' => $user->email],
                message: 'Your password has been reset successfully. You can now login with your new password.',
                code: 200
            );

        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
