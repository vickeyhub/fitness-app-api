<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

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

            $trainers = User::select('id','first_name','last_name','email')
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

    public function getBookingsFromUsers(){
        $user = Auth::user();

        // Fetch bookings based on user type
        $query = Booking::query();
        $query->select('id','user_id','session_id','payment_id','booking_date','time_slot','status','payment_status');
        if ($user->user_type === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->user_type === 'trainer') {
            $query->where('trainer_id', $user->id);
        } elseif ($user->user_type === 'gym') {
            $query->where('gym_id', $user->id);
        }
        // return $query->get();

        $bookings = $query->with([
            'user:id,first_name,last_name,email,mobile_number',
            'user.profile:profile_picture,age,dob,weight,weight_parameter,gender,location,specialty',
            // 'trainer',
            // 'gym',
            'session:id,session_title,duration,total_duration,calories,schedule,price,session_thumbnail,session_timing,session_type,is_publish',
            // 'payment:customer_id,email'
            // 'payment:id,payment_intent_id,amount,currency,status,email,name'
            ])
        // ->toSql();
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'User bookings fetched successfully!',
            'bookings' => $bookings
        ], 200);
    }
}
