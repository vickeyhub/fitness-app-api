<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;

class SessionFilterController extends Controller
{
    public function session_filter_data(Request $request)
    {
        try {
            $data = Classes::select('session_type', 'fitness_goal', 'duration', 'intensity')->get();

            $durations = $data->pluck('duration')->unique()->sort()->values();

            $durationRanges = [];

            for ($i = 0; $i < $durations->count() - 1; $i++) {
                $start = $durations[$i];
                $end = $durations[$i + 1];

                $durationRanges[] = "{$start} min - {$end} min";
            }

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
                    // 'data' => $data->pluck('duration')->unique()->values()
                    // 'data' => $data->pluck('duration')->unique()->values()->map(function ($item) {
                    //     return (string) $item;
                    // })
                    'data' => $durationRanges
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Message ' . $e->getMessage(),

            ], 500);
        }

    }
}
