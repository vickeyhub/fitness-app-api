<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgoraUserService
{
    protected $agoraService;
    protected $chatService;

    public function __construct()
    {
        $this->agoraService = new AgoraService();
        $this->chatService = new AgoraChatService();
    }

    /**
     * Register user for both Agora Chat and Video calling
     */
    public function registerUserForAgora($userData)
    {
        try {
            // Step 1: Create user in Laravel database
            $user = $this->createLaravelUser($userData);

            // Step 2: Register user in Agora Chat
            $chatRegistration = $this->registerUserInChat($user);

            // Step 3: Generate all tokens
            $tokens = $this->generateAllTokens($user);

            return [
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'agora_chat_username' => $user->agora_chat_username
                    ],
                    'chat_registration' => $chatRegistration,
                    'tokens' => $tokens
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to register user for Agora', [
                'error' => $e->getMessage(),
                'user_data' => $userData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to register user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create user in Laravel database
     */
    protected function createLaravelUser($userData)
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'agora_chat_username' => 'user_' . time() . '_' . Str::random(6)
        ]);

        Log::info('Laravel user created', ['user_id' => $user->id]);
        return $user;
    }

    /**
     * Register user in Agora Chat
     */
    protected function registerUserInChat($user)
    {
        $chatUserData = [
            'username' => $user->agora_chat_username,
            'password' => Str::random(12),
            'nickname' => $user->name
        ];

        $result = $this->chatService->registerUser($chatUserData);

        if ($result['success']) {
            // Update user with chat username
            $user->update(['agora_chat_username' => $chatUserData['username']]);
            Log::info('User registered in Agora Chat', ['username' => $chatUserData['username']]);
        }

        return $result;
    }

    /**
     * Generate all tokens for user
     */
    protected function generateAllTokens($user)
    {
        $tokens = [];

        // Generate RTM token for real-time messaging
        $rtmToken = $this->agoraService->generateRtmToken($user->id);
        if ($rtmToken) {
            $tokens['rtm_token'] = $rtmToken;
        }

        // Generate RTC token for video calling
        $rtcToken = $this->agoraService->generateRtcToken('default_channel', $user->id);
        if ($rtcToken) {
            $tokens['rtc_token'] = $rtcToken;
        }

        // Get chat access token
        $chatToken = $this->agoraService->getChatAccessToken();
        if ($chatToken) {
            $tokens['chat_token'] = $chatToken;
        }

        // Generate app access token
        $appToken = $this->agoraService->generateAppAccessToken($user->id);
        if ($appToken) {
            $tokens['app_access_token'] = $appToken;
        }

        return $tokens;
    }

    /**
     * Register existing user for Agora services
     */
    public function registerExistingUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Check if already registered in chat
            if (!$user->agora_chat_username) {
                $chatRegistration = $this->registerUserInChat($user);
                if (!$chatRegistration['success']) {
                    throw new \Exception('Failed to register in Agora Chat');
                }
            }

            // Generate tokens
            $tokens = $this->generateAllTokens($user);

            return [
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'agora_chat_username' => $user->agora_chat_username
                    ],
                    'tokens' => $tokens
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to register existing user for Agora', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to register user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user's Agora status and tokens
     */
    public function getUserAgoraStatus($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $status = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'chat_registered' => !empty($user->agora_chat_username),
                'agora_chat_username' => $user->agora_chat_username
            ];

            // Generate tokens if user is registered
            if ($status['chat_registered']) {
                $status['tokens'] = $this->generateAllTokens($user);
            }

            return [
                'success' => true,
                'data' => $status
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get user Agora status', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get user status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk register multiple users
     */
    public function bulkRegisterUsers($usersData)
    {
        $results = [];

        foreach ($usersData as $userData) {
            $result = $this->registerUserForAgora($userData);
            $results[] = [
                'email' => $userData['email'],
                'result' => $result
            ];
        }

        return [
            'success' => true,
            'data' => [
                'total_users' => count($usersData),
                'results' => $results
            ]
        ];
    }
}
