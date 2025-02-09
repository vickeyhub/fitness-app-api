<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserProfile;

class GymsController extends Controller
{
    public function getGyms(Request $request){
        try {
            // Validation Rules
            $validator = Validator::make($request->all(), [
                'location' => 'nullable|string|max:255',
                'rating' => 'nullable|numeric|min:0|max:5',
                'specialty' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    // 'errors' => $validator->errors()
                    'errors' => array_values($validator->errors()->all())
                ], 422);
            }
            $name = $request->name;
            $location = $request->location;
            $rating = $request->rating;
            $specialty = $request->specialty;

            $trainers = User::select('id','name as first_name','last_name','email')
            ->where(['user_type' => 'gym','status' => '1'])
                ->when($name, function ($query, $name) {
                    $query->where('name', 'LIKE', "%$name%")
                        ->orWhere('last_name', 'LIKE', "%$name%");
                })
                ->whereHas('profile', function ($query) use ($location, $rating, $specialty) {
                    $query->when($location, fn($q) => $q->where('location', 'LIKE', "%$location%"))
                        ->when($rating, fn($q) => $q->where('rating', 'LIKE', "%$rating%"))
                        ->when($specialty, fn($q) => $q->where('specialty', 'LIKE', "%$specialty%"));
                })
                ->with('profile:id,user_id,specialty,rating,location')
                ->get();
            if($trainers->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No gyms are available'
                ], 200);
            }

            return response()->json($trainers, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
