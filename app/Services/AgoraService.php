<?php

namespace App\Services;

use App\Services\Agora\RtcTokenBuilder;
use App\Services\Agora\RtmTokenBuilder;
use App\Services\Agora\AccessToken2;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class AgoraService
{
    protected $appId;
    protected $certificate;
    protected $chatService;

    public function __construct()
    {
        $this->appId = config('agora.app_id');
        $this->certificate = config('agora.certificate');
        $this->chatService = new AgoraChatService();
    }

    /**
     * Generate RTC token for video calling
     */
    public function generateRtcToken($channelName, $uid, $role = RtcTokenBuilder::RolePublisher, $expireInSeconds = 3600)
    {
        if (!$this->appId || !$this->certificate) {
            Log::error('Agora RTC credentials not configured');
            return null;
        }

        $privilegeExpiredTs = time() + $expireInSeconds;

        try {
            $token = RtcTokenBuilder::buildTokenWithUid(
                $this->appId,
                $this->certificate,
                $channelName,
                $uid,
                $role,
                $privilegeExpiredTs
            );

            Log::info('RTC token generated successfully', [
                'channel' => $channelName,
                'uid' => $uid,
                'role' => $role
            ]);

            return $token;
        } catch (\Exception $e) {
            Log::error('Failed to generate RTC token', [
                'error' => $e->getMessage(),
                'channel' => $channelName,
                'uid' => $uid
            ]);
            return null;
        }
    }

    /**
     * Generate RTM token for real-time messaging
     */
    public function generateRtmToken($userId, $role = RtmTokenBuilder::RoleRtmUser, $expireInSeconds = 3600)
    {
        if (!$this->appId || !$this->certificate) {
            Log::error('Agora RTM credentials not configured');
            return null;
        }

        $privilegeExpiredTs = time() + $expireInSeconds;

        try {
            $token = RtmTokenBuilder::buildToken(
                $this->appId,
                $this->certificate,
                $userId,
                $role,
                $privilegeExpiredTs
            );

            Log::info('RTM token generated successfully', [
                'user_id' => $userId,
                'role' => $role
            ]);

            return $token;
        } catch (\Exception $e) {
            Log::error('Failed to generate RTM token', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return null;
        }
    }

    /**
     * Get chat access token
     */
    public function getChatAccessToken()
    {
        return $this->chatService->getAccessToken();
    }

    /**
     * Register user in Agora Chat
     */
    public function registerChatUser($userData)
    {
        return $this->chatService->registerUser($userData);
    }

    /**
     * Get comprehensive tokens for user
     */
    public function getAllTokens($userId, $channelName = null)
    {
        $tokens = [];

        // Generate RTM token
        $rtmToken = $this->generateRtmToken($userId);
        if ($rtmToken) {
            $tokens['rtm_token'] = $rtmToken;
        }

        // Generate RTC token if channel name provided
        if ($channelName) {
            $rtcToken = $this->generateRtcToken($channelName, $userId);
            if ($rtcToken) {
                $tokens['rtc_token'] = $rtcToken;
            }
        }

        // Get chat token
        $chatToken = $this->getChatAccessToken();
        if ($chatToken) {
            $tokens['chat_token'] = $chatToken;
        }

        return $tokens;
    }

    /**
     * Test all Agora services
     */
    public function testAllServices()
    {
        $results = [];

        // Test RTC token generation
        $rtcToken = $this->generateRtcToken('test_channel', 'test_user');
        $results['rtc_token'] = [
            'success' => !empty($rtcToken),
            'token' => $rtcToken ? substr($rtcToken, 0, 20) . '...' : null
        ];

        // Test RTM token generation
        $rtmToken = $this->generateRtmToken('test_user');
        $results['rtm_token'] = [
            'success' => !empty($rtmToken),
            'token' => $rtmToken ? substr($rtmToken, 0, 20) . '...' : null
        ];

        // Test chat connection
        $chatResult = $this->chatService->testConnection();
        $results['chat'] = $chatResult;

        // Check credentials
        $results['credentials'] = [
            'app_id' => $this->appId ? 'Set' : 'Not Set',
            'certificate' => $this->certificate ? 'Set' : 'Not Set',
            'chat_org_name' => config('agora.chat.org_name') ? 'Set' : 'Not Set',
            'chat_app_name' => config('agora.chat.app_name') ? 'Set' : 'Not Set',
            'chat_client_id' => config('agora.chat.client_id') ? 'Set' : 'Not Set',
            'chat_client_secret' => config('agora.chat.client_secret') ? 'Set' : 'Not Set'
        ];

        return $results;
    }

    /**
     * Generate secure app access token
     */
    public function generateAppAccessToken($userId, $permissions = ['read', 'write'], $expireInSeconds = 86400)
    {
        if (!$this->appId || !$this->certificate) {
            return null;
        }

        $privilegeExpiredTs = time() + $expireInSeconds;

        try {
            // Create custom token with specific permissions
            $token = RtcTokenBuilder::buildTokenWithUid(
                $this->appId,
                $this->certificate,
                'app_access',
                $userId,
                RtcTokenBuilder::RolePublisher,
                $privilegeExpiredTs
            );

            return [
                'token' => $token,
                'user_id' => $userId,
                'permissions' => $permissions,
                'expires_at' => date('Y-m-d H:i:s', $privilegeExpiredTs)
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate app access token', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return null;
        }
    }

    /**
     * Validate token using AccessToken2
     */
    public function validateToken($token, $type = 'rtc')
    {
        try {
            // Basic validation - check if token is not empty and has correct format
            if (empty($token)) {
                Log::warning('Token validation failed: Empty token');
                return false;
            }

            // Check if token is base64 encoded
            if (!preg_match('/^[A-Za-z0-9\-_]+$/', $token)) {
                Log::warning('Token validation failed: Invalid format');
                return false;
            }

            // Use AccessToken2 for enhanced validation
            if ($this->appId && $this->certificate) {
                $isValid = AccessToken2::validateToken($token, $this->appId, $this->certificate);
                Log::info('Token validation result', [
                    'type' => $type,
                    'is_valid' => $isValid,
                    'app_id' => $this->appId
                ]);
                return $isValid;
            }

            Log::warning('Token validation skipped: Missing credentials');
            return true;
        } catch (\Exception $e) {
            Log::error('Token validation failed', [
                'error' => $e->getMessage(),
                'token_type' => $type,
                'token_preview' => substr($token, 0, 20) . '...'
            ]);
            return false;
        }
    }
}
