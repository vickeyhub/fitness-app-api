<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgoraController extends Controller
{
    protected $agoraService;

    public function __construct()
    {
        $this->agoraService = new AgoraService();
    }

    /**
     * Generate RTC token for video calling
     */
    public function generateRtcToken(Request $request)
    {
        $request->validate([
            'channel_name' => 'required|string|max:64',
            'uid' => 'required|string|max:32',
            'role' => 'integer|in:0,1,2,101',
            'expire_in' => 'integer|min:60|max:86400'
        ]);

        $channelName = $request->channel_name;
        $uid = $request->uid;
        $role = $request->role ?? 1; // Default to Publisher
        $expireIn = $request->expire_in ?? 3600; // Default 1 hour

        $token = $this->agoraService->generateRtcToken($channelName, $uid, $role, $expireIn);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate RTC token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'channel_name' => $channelName,
                'uid' => $uid,
                'role' => $role,
                'expires_in' => $expireIn
            ]
        ]);
    }

    /**
     * Generate RTM token for real-time messaging
     */
    public function generateRtmToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|max:32',
            'role' => 'integer|in:1',
            'expire_in' => 'integer|min:60|max:86400'
        ]);

        $userId = $request->user_id;
        $role = $request->role ?? 1; // Default to RTM User
        $expireIn = $request->expire_in ?? 3600; // Default 1 hour

        $token = $this->agoraService->generateRtmToken($userId, $role, $expireIn);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate RTM token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user_id' => $userId,
                'role' => $role,
                'expires_in' => $expireIn
            ]
        ]);
    }

    /**
     * Generate secure app access token
     */
    public function generateAppAccessToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|max:32',
            'permissions' => 'array',
            'expire_in' => 'integer|min:60|max:86400'
        ]);

        $userId = $request->user_id;
        $permissions = $request->permissions ?? ['read', 'write'];
        $expireIn = $request->expire_in ?? 86400; // Default 24 hours

        $result = $this->agoraService->generateAppAccessToken($userId, $permissions, $expireIn);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate app access token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Get all tokens for authenticated user
     */
    public function getAllTokens(Request $request)
    {
        $request->validate([
            'channel_name' => 'string|max:64'
        ]);

        $user = Auth::user();
        $channelName = $request->channel_name;

        $tokens = $this->agoraService->getAllTokens($user->id, $channelName);

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate tokens'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $tokens
        ]);
    }

    /**
     * Test all Agora services
     */
    public function testAllServices()
    {
        $results = $this->agoraService->testAllServices();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Validate token
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'type' => 'string|in:rtc,rtm,chat'
        ]);

        $token = $request->token;
        $type = $request->type ?? 'rtc';

        $isValid = $this->agoraService->validateToken($token, $type);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => substr($token, 0, 20) . '...',
                'type' => $type,
                'is_valid' => $isValid
            ]
        ]);
    }

    /**
     * Debug token (for development)
     */
    public function debugToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $token = $request->token;
        $appId = config('agora.app_id');
        $certificate = config('agora.certificate');

        try {
            // Decode token
            $data = base64_decode(strtr($token, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($token)) % 4));

            if ($data === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to decode token'
                ]);
            }

            // Try to unpack message
            $message = \App\Services\Agora\Message::unpack($data);

            return response()->json([
                'success' => true,
                'data' => [
                    'token_length' => strlen($token),
                    'decoded_length' => strlen($data),
                    'app_id' => $message->appID,
                    'channel_name' => $message->channelName,
                    'uid' => $message->uid,
                    'timestamp' => $message->ts,
                    'timestamp_readable' => date('Y-m-d H:i:s', $message->ts),
                    'privileges_count' => count($message->privileges),
                    'privileges' => $message->privileges,
                    'expected_app_id' => $appId,
                    'app_id_match' => $message->appID === $appId,
                    'certificate_set' => !empty($certificate)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token debug failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get Agora configuration status
     */
    public function getConfigStatus()
    {
        $config = [
            'app_id' => config('agora.app_id') ? 'Set' : 'Not Set',
            'certificate' => config('agora.certificate') ? 'Set' : 'Not Set',
            'chat_org_name' => config('agora.chat.org_name') ? 'Set' : 'Not Set',
            'chat_app_name' => config('agora.chat.app_name') ? 'Set' : 'Not Set',
            'chat_client_id' => config('agora.chat.client_id') ? 'Set' : 'Not Set',
            'chat_client_secret' => config('agora.chat.client_secret') ? 'Set' : 'Not Set'
        ];

        $allSet = !in_array('Not Set', $config);

        return response()->json([
            'success' => true,
            'data' => [
                'config' => $config,
                'all_configured' => $allSet
            ]
        ]);
    }
}
