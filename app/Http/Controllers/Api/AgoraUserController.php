<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgoraUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AgoraUserController extends Controller
{
    protected $agoraUserService;

    public function __construct()
    {
        $this->agoraUserService = new AgoraUserService();
    }

    /**
     * Register new user for Agora services
     */
    public function registerNewUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'channel_name' => 'string|max:64'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userData = $request->only(['name', 'email', 'password']);
        $result = $this->agoraUserService->registerUserForAgora($userData);

        if ($result['success']) {
            return response()->json($result, 201);
        } else {
            return response()->json($result, 500);
        }
    }

    /**
     * Register existing user for Agora services
     */
    public function registerExistingUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->agoraUserService->registerExistingUser($request->user_id);

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 500);
        }
    }

    /**
     * Register current authenticated user for Agora services
     */
    public function registerCurrentUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $result = $this->agoraUserService->registerExistingUser($user->id);

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 500);
        }
    }

    /**
     * Get user's Agora status and tokens
     */
    public function getUserStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->agoraUserService->getUserAgoraStatus($request->user_id);

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 500);
        }
    }

    /**
     * Get current user's Agora status and tokens
     */
    public function getCurrentUserStatus()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $result = $this->agoraUserService->getUserAgoraStatus($user->id);

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 500);
        }
    }

    /**
     * Bulk register multiple users
     */
    public function bulkRegisterUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|min:1',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->agoraUserService->bulkRegisterUsers($request->users);

        if ($result['success']) {
            return response()->json($result, 201);
        } else {
            return response()->json($result, 500);
        }
    }

    /**
     * Test user registration with sample data
     */
    public function testRegistration()
    {
        $sampleUserData = [
            'name' => 'Test User ' . time(),
            'email' => 'test_' . time() . '@example.com',
            'password' => 'password123'
        ];

        $result = $this->agoraUserService->registerUserForAgora($sampleUserData);

        return response()->json([
            'success' => true,
            'data' => [
                'test_user_data' => $sampleUserData,
                'registration_result' => $result
            ]
        ]);
    }

    /**
     * Get all registered Agora users
     */
    public function getAllAgoraUsers()
    {
        try {
            $users = \App\Models\User::whereNotNull('agora_chat_username')
                ->select('id', 'name', 'email', 'agora_chat_username', 'created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $users->count(),
                    'users' => $users
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get users: ' . $e->getMessage()
            ], 500);
        }
    }
}
