<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    // 1. Upload Status
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media' => 'required|file|mimes:jpeg,jpg,png,mp4,mov|max:10240',
            'type' => 'required|in:photo,video',
            'caption' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => array_values($validator->errors()->all())
            ], 422);
        }

        $path = $request->file('media')->store('statuses', 'public');

        $status = Status::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'media' => $path,
            'caption' => $request->caption
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status uploaded successfully.',
            'statusId' => $status->id
        ]);
    }

    // 2. Get Status Feed
    public function feed()
    {
        $user = Auth::user();

        // Users followed by the current user
        $followingIds = $user->following()->pluck('users.id')->toArray();

        // Users who follow the current user
        $followerIds = $user->followers()->pluck('users.id')->toArray();

        // Mutual followers
        $mutualFollowerIds = array_intersect($followingIds, $followerIds);

        // Fetch statuses from mutual followers in the last 24 hours
        $statuses = Status::whereIn('user_id', $mutualFollowerIds)
            ->where('is_hidden', false)
            ->where('created_at', '>=', now()->subDay())
            ->with('user.profile')
            ->latest()
            ->get()
            ->groupBy('user_id');

        $response = [];

        foreach ($statuses as $userId => $userStatuses) {
            $userInfo = $userStatuses->first()->user;
            $response[] = [
                'userId' => $userId,
                'userName' => $userInfo->first_name . ' ' . $userInfo->last_name,
                'profileImage' => $userInfo->profile->profile_picture ?? null,
                'statusList' => $userStatuses->map(function ($status) {
                    return [
                        'statusId' => $status->id,
                        'type' => $status->type,
                        'mediaUrl' => asset('storage/' . $status->media),
                        'caption' => $status->caption,
                        'createdAt' => $status->created_at
                    ];
                })->values()
            ];
        }

        return response()->json([
            'status' => "success",
            'data' => $response
        ]);
    }

    // 3. Get My Statuses
    public function myStatuses()
    {
        $statuses = Status::where('user_id', Auth::id())
            ->where('is_hidden', false)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->latest()
            ->get();

        return response()->json([
            'status' => "success",
            'data' => [
                [
                    'userId' => Auth::id(),
                    'userName' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                    'profileImage' => Auth::user()->profile->profile_picture ?? null,
                    'statusList' => $statuses->map(function ($status) {
                        return [
                            'statusId' => $status->id,
                            'type' => $status->type,
                            'mediaUrl' => asset('storage/' . $status->media),
                            'caption' => $status->caption,
                            'createdAt' => $status->created_at
                        ];
                    })->values()
                ]
            ]
        ]);
    }

    // 4. Delete Status
    public function delete($id)
    {
        try {
            $status = Status::where('id', $id)->where('user_id', Auth::id())->first();

            if (!$status) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Status not found or you are not authorized to delete this status.'
                ], 404);
            }

            Storage::disk('public')->delete($status->media);
            $status->delete();

            return response()->json([
                'status' => "success",
                'message' => 'Status deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
