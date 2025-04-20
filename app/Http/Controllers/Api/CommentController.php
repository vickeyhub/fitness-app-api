<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $data['comment'],
        ]);

        // return response()->json($comment->load('user'), 201);
        return response()->json([
            'status' => 'success',
            'message' => 'Comment has been created',
            'data' => $comment->load('user:id,first_name,last_name,user_type'),
            // $post->load('tags')
        ], 201);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();
        // return response()->json(null, 204);
        return response()->json([
            'status' => 'success',
            'message' => 'comment has been removed successfully.',
            // $post->load('tags')
        ], 200);
    }
}
