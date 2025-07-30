<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgoraChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgoraChatController extends Controller
{
    protected $agoraChatService;

    public function __construct(AgoraChatService $agoraChatService)
    {
        $this->agoraChatService = $agoraChatService;
    }

    /**
     * Test Agora Chat connection
     */
    public function testConnection()
    {
        $result = $this->agoraChatService->testConnection();
        return response()->json($result);
    }

    /**
     * Register user in Agora Chat with custom data
     */
    public function registerUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'nickname' => 'nullable|string|max:100',
            'avatar' => 'nullable|string|url'
        ]);

        $userData = $request->only(['username', 'password', 'nickname', 'avatar']);

        // If no nickname provided and user is authenticated, use their name
        if (empty($userData['nickname']) && Auth::check()) {
            $user = Auth::user();
            $userData['nickname'] = $user->first_name . ' ' . $user->last_name;
        }

        $result = $this->agoraChatService->registerUser($userData);

        if ($result['success'] && Auth::check()) {
            // Save Agora username to user record
            $user = Auth::user();
            $user->update(['agora_chat_username' => $userData['username']]);
        }

        return response()->json($result);
    }

    /**
     * Auto register user in Agora Chat (during app registration)
     */
    public function autoRegisterUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Check if user is already registered
        if ($user->agora_chat_username) {
            return response()->json([
                'success' => true,
                'message' => 'User already registered in Agora Chat',
                'agora_username' => $user->agora_chat_username,
                'already_registered' => true
            ]);
        }

        // Generate unique username and password
        $username = 'user_' . $user->id . '_' . time();
        $password = 'agora_' . Str::random(12);

        $userData = [
            'username' => $username,
            'password' => $password,
            'nickname' => $user->first_name . ' ' . $user->last_name,
            'avatar' => $user->profile?->profile_picture ?? ''
        ];

        Log::info('Attempting to register user in Agora Chat', [
            'user_id' => $user->id,
            'username' => $username
        ]);

        $result = $this->agoraChatService->registerUser($userData);

        if ($result['success']) {
            // Save Agora username to user record
            $user->update(['agora_chat_username' => $username]);

            Log::info('User successfully registered in Agora Chat', [
                'user_id' => $user->id,
                'agora_username' => $username
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered in Agora Chat successfully',
                'agora_username' => $username,
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email
                ]
            ]);
        }

        Log::error('Failed to register user in Agora Chat', [
            'user_id' => $user->id,
            'error' => $result['message']
        ]);

        return response()->json($result);
    }

    /**
     * Get chat token for user
     */
    public function getChatToken()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Check if user is registered in Agora Chat
        if (!$user->agora_chat_username) {
            // Auto register user
            $registerResult = $this->autoRegisterUser();
            if (!$registerResult->getData()->success) {
                return $registerResult;
            }
            // Refresh user data after registration
            $user->refresh();
        }

        // Get access token
        $token = $this->agoraChatService->getAccessToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chat token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'chat_token' => $token,
            'agora_username' => $user->agora_chat_username,
            'user_info' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'profile_picture' => $user->profile?->profile_picture
            ]
        ]);
    }

    /**
     * Get all users for chat
     */
    public function getChatUsers()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Get users from your database for chat
        $users = \App\Models\User::where('id', '!=', $user->id)
            ->where('status', '1')
            ->select('id', 'first_name', 'last_name', 'email', 'agora_chat_username')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'agora_username' => $user->agora_chat_username,
                    'profile_picture' => $user->profile?->profile_picture
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $users,
            'total_users' => $users->count()
        ]);
    }

    /**
     * Check user registration status
     */
    public function checkRegistrationStatus()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'is_registered' => !empty($user->agora_chat_username),
            'agora_username' => $user->agora_chat_username,
            'user_info' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email
            ]
        ]);
    }
}
