<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\GetStreamService;
use App\Models\User;

class GetStreamController extends Controller
{
    protected GetStreamService $getStreamService;

    public function __construct()
    {
        $this->getStreamService = new GetStreamService();
    }

    /**
     * Generate user token for GetStream
     */
    public function generateToken(Request $request)
    {
        try {
            $user = $request->user();
            $result = $this->getStreamService->generateToken($user);

            if ($result['success']) {
                return response()->json([
                    'token' => $result['token'],
                    'streamUserId' => $result['stream_user_id'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception in generateToken', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createChannel(Request $request)
    {
        $request->validate([
            'channel_type' => 'required|string', // e.g. messaging
            'channel_id' => 'required|string', // e.g. room-1
            'members' => 'required|array',  // user IDs
        ]);

        $channelType = $request->channel_type;
        $channelId = $request->channel_id;
        $members = $request->members;
        $creatorId = 'user-' . $request->user()->id;

        $result = $this->getStreamService->createChannel($channelType, $channelId, $members, $creatorId);

        if ($result['success']) {
            return response()->json([
                'channel_id' => $channelId,
                'channel_type' => $channelType,
                'members' => $members,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 500);
        }
    }
    public function registerUser(Request $request)
    {
        $users = User::all();
        $this->getStreamService->registerMultipleUsers($users);
        return response()->json(['message' => 'Users registered successfully']);
    }
}
