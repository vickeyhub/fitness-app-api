<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class BookingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch bookings based on user type
        $query = Booking::query();

        if ($user->user_type === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->user_type === 'trainer') {
            $query->where('trainer_id', $user->id);
        } elseif ($user->user_type === 'gym') {
            $query->where('gym_id', $user->id);
        }
        // return $query->get();

        $bookings = $query->with(['trainer', 'gym'])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'User bookings fetched successfully!',
            'bookings' => $bookings
        ], 200);
    }
    public function createBookings(Request $request)
    {
        try {
            // $payload = $request->all();
            $validator = Validator::make($request->all(), [
                'trainer' => 'nullable|exists:users,id',
                'gym' => 'nullable|exists:users,id',
                'session_id' => 'required',
                'booking_date' => 'required|date|after_or_equal:today',
                'time_slot' => 'required|string',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    // 'errors' => $validator->errors()
                    'errors' => array_values($validator->errors()->all())
                ], 422);
            }

            // Ensure the selected trainer & gym exist and have correct user_type
            if ($request->trainer) {
                $trainer = User::where('id', $request->trainer)->where('user_type', 'trainer')->first();
                if (!$trainer) {
                    return response()->json([
                        'status' => "failed",
                        'message' => 'Invalid trainer ID'
                    ], 400);
                }
            }

            if ($request->gym) {
                $gym = User::where('id', $request->gym)->where('user_type', 'gym')->first();
                if (!$gym) {
                    return response()->json(['status' => "failed", 'message' => 'Invalid gym ID'], 400);
                }
            }
            // return $request->all();

            // $booking = Booking::create([
            //     'user_id' => Auth::id(),
            //     'trainer_id' => $request->trainer,
            //     'gym_id' => $request->gym,
            //     'session_id' => $request->session_id,
            //     'booking_date' => $request->booking_date,
            //     'time_slot' => $request->time_slot,
            //     'status' => '1',
            // ]);

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'trainer_id' => $request->trainer,
                'gym_id' => $request->gym,
                'session_id' => $request->session_id,
                'payment_id' => $request->payment_id,
                'booking_date' => $request->booking_date,
                'time_slot' => $request->time_slot,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
            ]);

            return response()->json([
                'message' => 'Booking confirmed successfully!',
                'booking' => $booking
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
             ], 500);
        }
    }
}
