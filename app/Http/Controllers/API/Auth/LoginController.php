<?php

namespace App\Http\Controllers\API\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use ResponseTrait;

    public function Login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            if (filter_var($request->email, FILTER_VALIDATE_EMAIL) !== false) {
                $user = User::withTrashed()->where('email', $request->email)->first();
                if (empty($user)) {
                    return $this->sendError('User not found. Please check your email or register if you don\'t have an account.', 404);
                }
            }

            // Check the password
            if (!Hash::check($request->password, $user->password)) {
                return $this->sendError('Invalid credentials. Please check your email and password.', 401);
            }

            // Check if the email is verified before login is successful
            if (!$user->email_verified_at) {
                return $this->sendResponse('Please verify your email before login.', 401);
            }

            // Generate token if email is verified
            $token = $user->createToken('YourAppName')->plainTextToken;

            return $this->sendResponse(
                data: [
                    'token_type' => 'bearer',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_verified' => $user->is_verified,
                    ]
                ],
                message: 'Login successful. Your account is pending admin verification to access all features.',
                code: 200
            );
        } catch (Exception $e) {
            return $this->sendError('Something went wrong. Please try again later.', 500);
        }
    }

    public function refreshToken(): \Illuminate\Http\JsonResponse
    {
        try {
            $refreshToken = auth()->refresh();

            return $this->sendResponse(
                data: [
                    'token_type' => 'bearer',
                    'token' => $refreshToken,
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'user' => auth('api')->user()
                ],
                message: 'Access token refreshed successfully.',
                code: 200
            );
        } catch (Exception $e) {
           return $this->sendError('Failed to refresh token. Please try again.', 500);
        }
    }
}
