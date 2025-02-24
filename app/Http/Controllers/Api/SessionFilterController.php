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

        return response()->json([
            'categories' => $data->pluck('session_type')->flatten()->unique()->values(),
            'fitness_goals' => $data->pluck('fitness_goal')->flatten()->unique()->values(),
            'duration' => $data->pluck('duration')->unique()->values(),
            'intensities' => $data->pluck('intensity')->unique()->values(),
        ]);
    }
}
