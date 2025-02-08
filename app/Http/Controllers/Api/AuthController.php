<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    /**
     * Login API for users and gym owners
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate a new API token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return success response with the user data and token
        return response()->json([
            'message' => 'User logged in successfully!',
            'user' => $user,
            'token' => $token
        ], 200);
    }
    public function register(Request $request)
    {
        // return $payload = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed', // password_confirmation required
            'user_type' => 'required|in:admin,trainer,user',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => $validator->errors()
            ], 422);
        }
        $otp = rand(1000, 9999);
        // Create the user record in the database
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'otp' => $otp,  // Store OTP in DB
            'status' => '0', // Set status as pending
        ]);

        if ($user) {
            Mail::to($request->email)->send(new SendOtpMail($otp, $request->email));
        }

        return response()->json([
            'message' => 'User registered successfully! Please verify your email with OTP.',
            'user' => $user,
            // 'token' => $token
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid OTP or email.'], 400);
        }

        // Mark user as verified
        $user->update(['status' => '1', 'otp' => null]);

        // Generate API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully!',
            'token' => $token
        ], 200);
    }

}
