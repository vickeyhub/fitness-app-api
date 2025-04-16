<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionsController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::id();

        $query = Classes::select('classes.id', 'session_title', 'duration', 'session_thumbnail', 'calories', 'price', 'session_avrage_rating','is_publish','latitude','longitude','radius',
        'users.id as created_by',
        'users.first_name',
        'users.last_name',
        'users.email',
        'users.mobile_number',
        'users.user_type'
        )
        ->where('user_id', $user_id);

        if ($request->filled('is_publish')) {
            $query->where('is_publish', $request->is_publish);
        }
        $query->leftJoin('users', 'users.id','=', 'classes.user_id');
        $sessions = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'message' => "Session fetched successfully",
            'data' => $sessions->items(),
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'total' => $sessions->total(),
                'per_page' => $sessions->perPage(),
                'last_page' => $sessions->lastPage(),
            ]
        ], 200);
    }

    public function search_sessions(Request $request)
    {
        try{
            $query = Classes::query();

        $query->select('classes.id', 'classes.session_title', 'classes.duration', 'classes.session_thumbnail', 'classes.calories', 'classes.price', 'classes.session_avrage_rating','classes.is_publish','latitude','longitude','radius',
        DB::raw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance")
    );
        // $query->select('*');
        $query->addBinding([$request->latitude, $request->longitude, $request->latitude], 'select');

        if ($request->filled('session_title')) {
            $query->where('session_title', 'LIKE', '%' . $request->session_title . '%');
        }
        if ($request->filled('category')) {
            $query->where('session_type', 'LIKE', '%' . $request->category . '%');
        }

        if ($request->filled('duration')) {
            $duration_range = str_replace(' min', '', $request->duration);
            $duration_array = explode('-', $duration_range);
            if (count($duration_array) == 2) {
                $min_duration = $duration_array[0];
                $max_duration = $duration_array[1];

                $query->whereBetween('duration', [$min_duration, $max_duration]);
            }
            // $query->where('duration', 'LIKE', '%' . $request->duration . '%');

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
        $query->with('trainer');
        $sessions = $query->orderBy('id' ,'desc')->paginate(20);
        return response()->json([
            'status' => 'success',
            'message' => "Session fetched successfully",
            'data' => $sessions->items(),
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'total' => $sessions->total(),
                'per_page' => $sessions->perPage(),
                'last_page' => $sessions->lastPage(),
            ]
        ], 200);
        } catch(\Exception $e) {
            return response()->json([
               'status' => 'failed',
               'message' => $e->getMessage()
            ], 500);
        }
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
            if ($data = $query->where('classes.id', $id)->first()) {

                return response()->json([
                    'status' => 'success',
                    'message' => 'session detail fetched successfully',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No record found',
                    // 'data' => $data
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Message ' . $e->getMessage(),
                // 'data' => $data
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
        try {
            $validator = Validator::make($request->all(), [
                'session_title' => 'required|string|max:255',
                'description' => 'required|string',
                // 'duration' => 'required|string',
                'total_duration' => 'required|integer',
                'calories' => 'required|integer',
                'steps' => 'required|array',
                'muscles_involved' => 'required|array',
                'schedule' => 'required|array',
                // 'user_id' => 'required|string',
                'price' => 'required|numeric',
                'session_thumbnail' => 'required|string',
                'session_avrage_rating' => 'nullable|numeric|min:0|max:5',
                'session_timing' => 'required|string',
                // new addition in request 19-02-2025
                "session_type" => "required|array",
                "session_keywords" => "required|array",
                "intensity" => "required|string",
                "fitness_goal" => "required|array",
                "is_publish" => "required|boolean",
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|numeric', // Radius in KM
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => array_values($validator->errors()->all())
                ], 422);
            }
            $payload = $validator->validated();
            $session_timing = $payload['session_timing'];
            $timing_array = explode(' - ', $session_timing);

            // Convert times to Carbon instances
            $start = Carbon::createFromFormat('h:i a', trim($timing_array[0]));
            $end = Carbon::createFromFormat('h:i a', trim($timing_array[1]));
            $payload['duration'] = $start->diffInMinutes($end);
            $payload['user_id'] = Auth::user()->id;

            $session = Classes::create($payload);

            return response()->json(['message' => 'Session created successfully', 'session' => $session], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Message ' . $e->getMessage(),

            ], 500);
        }
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
        try {
            $user_id = Auth::id();
            $class = Classes::where('user_id', $user_id)->where('id', $id)->first();

            if (!$class) {
                return response()->json([
                    'status' => 'failed',
                    'message' => "Session not found or you don't have permission to delete it."
                ], 404);
            }

            if ($class->delete()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Session has been deleted successfully."
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => "Something went wrong."
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Delete Class Error: ' . $e->getMessage()); // Error Log
            return response()->json([
                'status' => 'error',
                'message' => "Internal Server Error."
            ], 500);
        }

    }
}
