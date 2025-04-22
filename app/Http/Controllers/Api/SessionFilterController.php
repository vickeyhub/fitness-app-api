<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;

class SessionFilterController extends Controller
{
    public function session_filter_data(Request $request)
    {
        $data = Classes::select('session_type', 'fitness_goal', 'duration', 'intensity')->get();

        $response = [
            'categories' => [
                'name' => 'Categories',
                'data' => $data->pluck('session_type')->flatten()->unique()->values()
            ],
            'fitness_goals' => [
                'name' => 'Fitness Goal',
                'data' => $data->pluck('fitness_goal')->flatten()->unique()->values()
            ],
            'duration' => [
                'name' => 'Duration',
                'data' => $data->pluck('duration')->unique()->values()
            ],
            'intensities' => [
                'name' => 'Intensities',
                'data' => $data->pluck('intensity')->unique()->values()
            ]
        ];



        return response()->json([
            'status' => 'success',
            'data' => $response
        ], 200);
    }
}
