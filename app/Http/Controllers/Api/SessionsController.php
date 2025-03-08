<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionsController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $sessions = Classes::where('user_id', $user_id)->paginate();

        return response()->json([
            'status' => 'success',
            'message' => "Session fetched successfully",
            'data' => $sessions
        ], 200);
    }

    public function search_sessions(Request $request)
    {
        $query = Classes::query();

        $query->select(
            'classes.*',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.mobile_number',
            'users.user_type',
        );
        if (Auth::guard('sanctum')->check()) {
            $authId = Auth::guard('sanctum')->id();
            $query->leftJoin('bookmarks', function ($join) use ($authId) {
                $join->on('bookmarks.session_id', '=', 'classes.id')
                    ->where('bookmarks.user_id', '=', $authId);
            });
            $query->addSelect(
                DB::raw('IF(bookmarks.id IS NOT NULL, true, false) as is_bookmarked')
            );
        }


        if ($request->filled('session_title')) {
            $query->where('session_title', 'LIKE', '%' . $request->session_title . '%');
        }
        if ($request->filled('category')) {
            $query->where('session_type', 'LIKE', '%' . $request->category . '%');
        }

        if ($request->filled('duration')) {
            $query->where('duration', 'LIKE', '%' . $request->duration . '%');
        }

        if ($request->filled('intensity')) {
            $query->where('intensity', 'LIKE', '%' . $request->intensity . '%');
        }

        if ($request->filled('fitness_goal')) {
            $fitnessGoals = is_array($request->fitness_goal) ? $request->fitness_goal : json_decode($request->fitness_goal, true);

            if (is_array($fitnessGoals)) {
                $query->where(function ($q) use ($fitnessGoals) {
                    foreach ($fitnessGoals as $goal) {
                        $q->orWhereJsonContains('fitness_goal', $goal);
                    }
                });
            }
        }
        $query->leftJoin('users', 'users.id', '=', 'classes.user_id');

        return $query->paginate(5);
        // return $query->orderBy('classes.id', 'DESC')->cursorPaginate(5);
    }
    public function session_detail($id)
    {
        try {
            $query = Classes::query();

            $query->select(
                'classes.*',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.mobile_number',
                'users.user_type',
            );
            if (Auth::guard('sanctum')->check()) {
                $authId = Auth::guard('sanctum')->id();
                $query->leftJoin('bookmarks', function ($join) use ($authId) {
                    $join->on('bookmarks.session_id', '=', 'classes.id')
                        ->where('bookmarks.user_id', '=', $authId);
                });
                $query->addSelect(
                    DB::raw('IF(bookmarks.id IS NOT NULL, true, false) as is_bookmarked')
                );
            }

            $query->leftJoin('users', 'users.id', '=', 'classes.user_id');
            if($data = $query->where('classes.id', $id)->first()){

                return response()->json([
                    'status' => 'success',
                    'message' => 'session detail fetched successfully',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No record found',
                    'data' => $data
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Message ' . $e->getMessage(),
                'data' => $data
            ], 401);
        }
    }

    public function save_bookmark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:classes,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => array_values($validator->errors()->all())
            ], 422);
        }
        // $bookmark = Bookmark::firstorCreate([
        //     'user_id' => Auth::id(),
        //     'session_id' => $request->session_id
        // ]);

        // if($bookmark){
        //     return response()->json([
        //         'message' => 'Session bookmarked successfully',
        //         'bookmark' => $bookmark
        //     ], 201);
        // }

        $user_id = Auth::id();
        $session_id = $request->session_id;
        // check if bookmark already exist or not
        $bookmark = Bookmark::where([
            'user_id' => $user_id,
            'session_id' => $session_id
        ])->first();
        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Bookmark removed successfully',
                'bookmarked' => false
            ]);
        } else {
            Bookmark::create([
                'user_id' => $user_id,
                'session_id' => $session_id
            ]);
            return response()->json(['status' => 'success', 'message' => 'Session bookmarked successfully', 'bookmarked' => true]);
        }
    }

    public function get_bookmarked_sessions()
    {
        $bookmarks = Bookmark::where('user_id', Auth::id())
            ->with('session')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Bookmarked sessions',
            'data' => $bookmarks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string',
            'total_duration' => 'required|integer',
            'calories' => 'required|integer',
            'steps' => 'required|array',
            'muscles_involved' => 'required|array',
            'schedule' => 'required|array',
            // 'user_id' => 'required|string',
            'price' => 'required|numeric',
            'session_thumbnail' => 'required|string',
            'session_avrage_rating' => 'required|numeric|min:0|max:5',
            'session_timing' => 'required|string',
            // new addition in request 19-02-2025
            "session_type" => "required|array",
            "session_keywords" => "required|array",
            "intensity" => "required|string",
            "fitness_goal" => "required|array"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => array_values($validator->errors()->all())
            ], 422);
        }
        $payload = $validator->validated();
        $payload['user_id'] = Auth::user()->id;

        $session = Classes::create($payload);

        return response()->json(['message' => 'Session created successfully', 'session' => $session], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
