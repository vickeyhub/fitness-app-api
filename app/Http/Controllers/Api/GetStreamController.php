<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use GetStream\StreamChat\Client;

class GetStreamController extends Controller
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.stream.key'),
            config('services.stream.secret')
        );
    }

    /**
     * Generate user token for GetStream
     */
    public function generateToken(Request $request)
    {
        // $request->validate([
        //     'user_id' => 'required|string|max:255'
        // ]);

        try {
            $user = $request->user();
            $streamUserId = 'user-' . $user->id;

            $this->client->upsertUser([
                'id' => $streamUserId,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);

            $token = $this->client->createToken($streamUserId);

            return response()->json([
                'token' => $token,
                'streamUserId' => $streamUserId,
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in generateToken', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id ?? 'unknown'
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

        $channel = $this->client->Channel($channelType, $channelId, [
            'name' => ucfirst($channelId),
            'members' => $members,
        ]);

        // Create the channel on Stream (if not exists)
        $channel->create($request->user()->id);

        return response()->json([
            'channel_id' => $channelId,
            'channel_type' => $channelType,
            'members' => $members,
        ]);
    }
}
