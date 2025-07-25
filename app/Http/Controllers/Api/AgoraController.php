<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AgoraService;
use App\Models\AgoraToken;

class AgoraController extends Controller
{
    public function token(Request $request, AgoraService $agoraService)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // If agora_uid is empty, generate and save a new one
        if (empty($user->agora_uid)) {
            $user->agora_uid = 'fitnofy_' . $user->id;
            $user->save();
        }

        $expiresIn = 3600;
        $token = $agoraService->generateRtmToken($user->agora_uid, $expiresIn);

        AgoraToken::create([
            'user_id' => $user->id,
            'agora_uid' => $user->agora_uid,
            'token' => $token,
            'generated_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'agora_uid' => $user->agora_uid,
            'app_id' => $agoraService->getAppId(),
            'expires_in' => $expiresIn,
        ]);
    }
}
