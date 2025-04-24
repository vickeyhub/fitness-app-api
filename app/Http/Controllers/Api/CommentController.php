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
    public function index(Post $post, Request $request){
        $comments = $post->comments()
        ->with(
            'user:id,first_name,last_name',
            'user.profile'
        ) // load user
        ->latest()
        ->cursorPaginate(10);

   $formatted = $comments->items();

    $data = array_map(function ($comment) {
        // return $comment->user->profile->profile_picture;
        return [
            'id' => $comment->id,
            'comment' => $comment->comment,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->first_name . ' ' . $comment->user->last_name,
                'profile_picture' => $comment->user->profile->profile_picture ? asset('storage/' . $comment->user->profile->profile_picture) : null,
            ],
            'time_ago' => $comment->created_at->diffForHumans(),
        ];
    }, $formatted);

    return response()->json([
        'status' => 'success',
        'message' => 'Post comments fetched',
        'data' => $data,
        'pagination' => [
            'per_page' => $comments->perPage(),
            'next_cursor' => optional($comments->nextCursor())?->encode(),
            'next_page_url' => $comments->nextPageUrl(),
            'prev_cursor' => optional($comments->previousCursor())?->encode(),
            'prev_page_url' => $comments->previousPageUrl(),
            'path' => $comments->path(),
        ]
    ], 200);
    }
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
