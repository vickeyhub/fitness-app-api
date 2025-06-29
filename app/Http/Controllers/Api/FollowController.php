<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Follow;


class FollowController extends Controller
{
    public function follow(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return response()->json(['message' => "You can't follow yourself"], 400);
        }

        $authUser->following()->syncWithoutDetaching([$user->id]);

        return response()->json(['status' => 'success', 'message' => 'Followed user successfully']);
    }

    public function unfollow(User $user)
    {
        $authUser = Auth::user();
        $authUser->following()->detach($user->id);

        return response()->json(['status' => 'success', 'message' => 'Unfollowed user successfully']);
    }

    public function followingList()
    {
        $authUser = Auth::user();

        $users = $authUser->following()
            ->with(['profile:id,user_id,profile_picture,gender,specialties'])
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email') // only these fields
            ->paginate(20);

        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'profile' => $user->profile, // already limited via select
                'followed_at' => $user->pivot, // optional if you need
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Users you follow',
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    public function followersList(User $user)
    {
        $followers = $user->followers()->with('profile')->paginate(20);

        $followers->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'profile' => $user->profile, // already limited via select
                'followed_at' => $user->pivot, // optional if you need
            ];
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Followers list',
            'data' => $followers->items(),
            'pagination' => [
                'current_page' => $followers->currentPage(),
                'total' => $followers->total(),
                'per_page' => $followers->perPage(),
                'last_page' => $followers->lastPage(),
            ]
        ]);
    }

    public function isFollowing(User $user)
    {
        $authUser = Auth::user();
        $isFollowing = $authUser->following()->where('following_id', $user->id)->exists();

        return response()->json([
            'status' => 'success',
            'message' => $isFollowing ? 'You are following this user' : 'You are not following this user',
            'following' => $isFollowing
        ]);
    }
}
