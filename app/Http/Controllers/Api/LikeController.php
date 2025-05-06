<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like(Post $post)
    {
        return $this->storeReaction($post, 'like');
    }

    public function dislike(Post $post)
    {
        return $this->storeReaction($post, 'dislike');
    }

    protected function storeReaction(Post $post, $type)
    {
        $user = Auth::user();

        // delete old like/dislike
        $post->likes()->where('user_id', $user->id)->delete();

        // create new like/dislike
        $like = $post->likes()->create([
            'user_id' => $user->id,
            'type' => $type,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => "reacted $type",
            'data' => $like,
            // $post->load('tags')
        ], 200);
    }

    protected function new_storeReaction(Post $post, $type)
{
    $user = Auth::user();

    $existing = $post->likes()->where('user_id', $user->id)->first();

    if ($type === 'like') {
        if ($existing && $existing->type === 'like') {
            // already liked → do nothing
            return response()->json([
                'status' => 'success',
                'message' => 'Already liked',
                'data' => $existing,
            ]);
        }

        // remove dislike if exists
        if ($existing && $existing->type === 'dislike') {
            $existing->delete();
        }

        // add new like
        $like = $post->likes()->create([
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Liked post',
            'data' => $like,
        ]);
    }

    if ($type === 'dislike') {
        // remove like if exists
        if ($existing && $existing->type === 'like') {
            $existing->delete();
        }

        // add new dislike (or replace old one)
        if ($existing) $existing->delete(); // remove old dislike to prevent duplicates

        $dislike = $post->likes()->create([
            'user_id' => $user->id,
            'type' => 'dislike',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Disliked post',
            'data' => $dislike,
        ]);
    }

    return response()->json(['status' => 'error', 'message' => 'Invalid reaction type'], 422);
}
}
