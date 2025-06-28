<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


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

    // fetch users list
    public function index(Request $request)
    {
        $query = User::with('profile:id,user_id,profile_picture')
            ->select('id', 'first_name', 'last_name', 'email')
            ->where('user_type', ['user', 'trainer', 'gym']);

        if ($request->has('q') && strlen($request->q) > 0) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->q}%")
                    ->orWhere('last_name', 'like', "%{$request->q}%")
                    ->orWhere('email', 'like', "%{$request->q}%");
            });
        }
        $users = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'message' => $request->has('q') ? 'Search results' : 'User list fetched',
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
            ]
        ], 200);
    }

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

        // Validate
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
            'file' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,pdf|max:5120',
            // 'specialties' => 'required|array',
            'specialties' => 'string',
            'trainer_services' => 'nullable|string',
            'user_description' => 'nullable|string|max:500',
            'experience_level' => 'nullable|in:beginner,intermediate,advanced',
            'gender' => 'nullable|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $profile = $user->profile;

        // Update User's name if changed
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/profile', $filename, 'public');
        }

        // Prepare profile data (always encode arrays)
        $profileData = [
            'profile_picture' => $filePath,
            'age' => $request->age,
            'weight' => $request->weight,
            'weight_parameter' => $request->weight_parameter,
            'height' => $request->height,
            'gender' => $request->gender,
            'height_parameter' => $request->height_parameter,
            'dob' => $request->dob,
            'location' => $request->location,
            'rating' => $request->rating,
            'specialties' => $request->specialties,
            'trainer_services' => $request->trainer_services,
            'user_description' => $request->user_description,
            'experience_level' => $request->experiance_level,
        ];


        // Remove null profile picture if not uploaded
        if (!$filePath && $profile) {
            unset($profileData['profile_picture']);
        }

        // Create or update profile
        if (!$profile) {
            $profileData['user_id'] = $user->id;
            $profile = UserProfile::create($profileData);
        } else {
            $profile->update($profileData);
        }

        return response()->json([
            'message' => $filePath ? 'Profile updated with media' : 'Profile updated',
            'user' => $user,
            // 'profile' => $profile,
            'file_url' => $filePath ? asset('storage/' . $filePath) : null,
        ], 200);
    }


    public function getTrainers(Request $request)
    {
        try {
            // Validation Rules
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:150',
                'gender' => 'nullable|in:male,female,other',
                'experience_level' => 'nullable|in:beginner,intermediate,advanced',
                'specialties' => 'nullable|string|max:255',
                'service_offered' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => array_values($validator->errors()->all())
                ], 422);
            }
            $name = $request->name;
            $gender = $request->gender;
            $experience_level = $request->experience_level;
            $specialties = $request->specialties;
            $service_offered = $request->service_offered;
            $location = $request->location;

            $trainers = User::select('id', 'first_name', 'last_name')
                ->where(['user_type' => 'trainer', 'status' => '1'])
                ->when($name, function ($query, $name) {
                    $query->where(function ($q) use ($name) {
                        $q->where('first_name', 'LIKE', "%$name%")
                            ->orWhere('last_name', 'LIKE', "%$name%");
                    });
                })
                ->when($gender, function ($query, $gender) {
                    $query->whereHas('profile', function ($q) use ($gender) {
                        $q->where('gender', $gender);
                    });
                })
                ->when($experience_level, function ($query, $experience_level) {
                    $query->whereHas('profile', function ($q) use ($experience_level) {
                        $q->where('experience_level', $experience_level);
                    });
                })
                ->when($specialties, function ($query, $specialties) {
                    $query->whereHas('profile', function ($q) use ($specialties) {
                        $q->where('specialties', 'LIKE', "%$specialties%");
                    });
                })
                ->when($service_offered, function ($query, $service_offered) {
                    $query->whereHas('profile', function ($q) use ($service_offered) {
                        $q->where('trainer_services', 'LIKE', "%$service_offered%");
                    });
                })
                ->when($location, function ($query, $location) {
                    $query->whereHas('profile', function ($q) use ($location) {
                        $q->where('location', 'LIKE', "%$location%");
                    });
                })
                ->with('profile:id,user_id,specialties,rating,location,gender,experience_level,trainer_services,profile_picture')
                ->paginate(20);
            if ($trainers->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No trainers are available'
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Trainers fetched successfully',
                'data' => $trainers->items(),
                'pagination' => [
                    'current_page' => $trainers->currentPage(),
                    'total' => $trainers->total(),
                    'per_page' => $trainers->perPage(),
                    'last_page' => $trainers->lastPage(),
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function findBuddy(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:150',
                'gender' => 'nullable|in:male,female,other',
                'activities' => 'nullable|array',
                'activities.*' => 'string|max:100',
                'location' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $filters = $validator->validated();

            $query = User::with('profile:id,user_id,profile_picture,gender,location,specialties')
                ->where('user_type', 'user')
                ->where('status', '1');

            // Filter by name
            if (!empty($filters['name'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('first_name', 'like', "%{$filters['name']}%")
                        ->orWhere('last_name', 'like', "%{$filters['name']}%");
                });
            }

            // Filter by gender
            if (!empty($filters['gender'])) {
                $query->whereHas('profile', function ($q) use ($filters) {
                    $q->where('gender', $filters['gender']);
                });
            }

            // Filter by location
            if (!empty($filters['location'])) {
                $query->whereHas('profile', function ($q) use ($filters) {
                    $q->where('location', 'like', "%{$filters['location']}%");
                });
            }

            // Filter by activities (specialties)
            if (!empty($filters['activities'])) {
                $query->whereHas('profile', function ($q) use ($filters) {
                    foreach ($filters['activities'] as $activity) {
                        $q->orWhereJsonContains('specialties', $activity);
                    }
                });
            }

            $buddies = $query->paginate(20);

            if ($buddies->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No matching buddies found',
                    'data' => [],
                    'pagination' => [
                        'current_page' => $buddies->currentPage(),
                        'per_page' => $buddies->perPage(),
                        'total' => $buddies->total(),
                        'last_page' => $buddies->lastPage(),
                    ]
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Buddy search results',
                'data' => $buddies->items(),
                'pagination' => [
                    'current_page' => $buddies->currentPage(),
                    'per_page' => $buddies->perPage(),
                    'total' => $buddies->total(),
                    'last_page' => $buddies->lastPage(),
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while searching for buddies',
                'error' => $e->getMessage()
            ], 500);
        }
    }




}
