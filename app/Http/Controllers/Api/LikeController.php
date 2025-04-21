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
}
