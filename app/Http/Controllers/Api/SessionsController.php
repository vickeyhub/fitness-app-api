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
    public function index()
    {
        return Classes::all();
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
