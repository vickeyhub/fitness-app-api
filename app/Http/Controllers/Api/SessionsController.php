<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $session_type = $request->get('category');
        // $duration = $request->get('duration');
        // $intensity = $request->get('intensity');
        // $fitness_goal = $request->get('fitness_goal');
        // return Classes::whereLike('session_type', "%$session_type%")
        // ->orWhereLike('duration', "%$duration%")
        // ->orWhereLike('intensity', "%$intensity%")
        // ->orWhereLike('fitness_goal', "%$fitness_goal%")
        // ->paginate(1);

        $query = Classes::query();

        if ($request->filled('category')) {
            $query->where('session_type', 'LIKE', '%' . $request->category . '%');
        }

        if ($request->filled('duration')) {
            $query->where('duration', 'LIKE', '%' . $request->duration . '%');
        }

        if ($request->filled('intensity')) {
            $query->where('intensity', 'LIKE', '%' . $request->intensity . '%');
        }

        // if ($request->filled('fitness_goal')) {
        //     $query->where('fitness_goal', 'LIKE', '%' . $request->fitness_goal . '%');
        // }

        if ($request->filled('fitness_goal')) {
            $fitnessGoals = json_decode($request->fitness_goal, true);
            if (is_array($fitnessGoals)) {
                $query->whereIn('fitness_goal', $fitnessGoals);
            }
        }

        return $query->paginate(5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer',
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
