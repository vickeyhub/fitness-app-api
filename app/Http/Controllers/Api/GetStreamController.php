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
    private $apiKey = 'kcfev3rnnt2m';
    private $apiSecret = '365mua92euxua7nunbhbcgctb4aa3pv9hkbyj46bxhdahv4s4zfsmf32xgzw9rmd';
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            $this->apiKey,
            $this->apiSecret
        );
    }

    /**
     * Generate user token for GetStream
     */
    public function generateToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|max:255'
        ]);

        try {
            $user = $request->user();
            $streamUserId = 'user-' . $user->id;

            $this->client->upsertUser([
                'id' => $streamUserId,
                'name' => $user->first_name.' '.$user->last_name,
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
}
