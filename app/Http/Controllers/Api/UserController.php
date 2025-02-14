<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    /**
     * Fetch user profile.
     *
     * GET /api/user/profile
     */
    // public function getProfile()
    // {
    //     $user = Auth::user();

    //     // Fetch user's profile along with the base user details
    //     $profile = UserProfile::where('user_id', $user->id)->first();

    //     return response()->json([
    //         'message' => 'User profile fetched successfully!',
    //         'user' => $user,
    //         'profile' => $profile
    //     ], 200);
    // }

    public function show()
    {
        $user = User::with('profile')->find(Auth::user()->id);

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Update user profile.
     *
     * PUT /api/user/profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        // Check if the user has a profile, or create one if it doesn't exist
        $profile = $user->profile;

        // Validation rules
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:1|max:150',
            'last_name' => 'required|string|min:1|max:150',
            'age' => 'nullable|numeric|min:1|max:150',
            'weight' => 'nullable|numeric',
            'weight_parameter' => 'nullable|in:kg,lb',
            'height' => 'nullable|numeric',
            'height_parameter' => 'nullable|in:cm,inch',
            'dob' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'specialty' => 'nullable|string|max:255',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);


        if (!$profile) {
            // If no profile exists, create a new one
            $profile = UserProfile::create([
                'user_id' => $user->id,
                // 'profile_picture' => $request->profile_picture,
                'age' => $request->age,
                'weight' => $request->weight,
                'weight_parameter' => $request->weight_parameter,
                'height' => $request->height,
                'gender' => $request->gender,
                'height_parameter' => $request->height_parameter,
                'dob' => $request->dob,
                'location' => $request->location,
                'rating' => $request->rating,
                'specialty' => $request->specialty,
            ]);
        } else {
            // Update existing profile
            $profile->update($request->only([
                'age',
                'weight',
                'weight_parameter',
                'height',
                'height_parameter',
                'gender',
                'dob',
                'location',
                'rating',
                'specialty'
            ]));
        }

        return response()->json([
            'message' => 'User profile updated successfully!',
            'user' => $user,
            // 'profile' => $profile
        ], 200);
    }

    public function getTrainers(Request $request)
    {
        try {
            // Validation Rules
            $validator = Validator::make($request->all(), [
                'location' => 'nullable|string|max:255',
                'rating' => 'nullable|numeric|min:0|max:5',
                'specialty' => 'nullable|string|max:255',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                    'errors' => $validator->errors()
                ], 422);
            }
            $name = $request->first_name;
            $location = $request->location;
            $rating = $request->rating;
            $specialty = $request->specialty;

            $trainers = User::select('id','first_name as first_name', 'last_name')
            ->where(['user_type' => 'trainer','status' => '1'])
                ->when($name, function ($query, $name) {
                    $query->where('first_name', 'LIKE', "%$name%")
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
                    'message' => 'No trainers are available'
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
