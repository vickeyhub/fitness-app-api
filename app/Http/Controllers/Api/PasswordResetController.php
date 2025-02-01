<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
        /**
     * Handle a forgot password request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $rawToken = Str::random(6);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($rawToken), // Store a hashed version of the token for security
                'created_at' => now(),
            ]
        );

        Mail::to($request->email)->send(new ResetPasswordMail($request->email, $rawToken));

        return response()->json([
            'message' => 'A reset code has been sent to your email. Please use it to reset your password.',
        ], 200);

    }

    /**
     * Handle the password reset request.
     *
     * POST /api/reset-password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve the reset token record for the given email
        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired reset token.'], 400);
        }

        // Verify the provided token against the hashed token stored in the database
        if (!Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'Invalid reset token.'], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the password reset record
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password updated successfully!',
        ], 200);
    }
}
