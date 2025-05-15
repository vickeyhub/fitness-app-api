<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Berkayk\OneSignal\OneSignalFacade as OneSignal;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
            'player_id' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        OneSignal::sendNotificationToUser(
            $request->message,
            $request->player_id,
            $url = null,
            $data = ["type" => "custom"],
            $buttons = null,
            $schedule = null,
            $request->title
        );

        return response()->json(['status' => 'sent']);
        } catch(\Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
