<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'trainer', 'gym', 'session', 'payment'])
            ->orderByDesc('id')
            ->paginate(20);

        $customers = User::query()
            ->where('user_type', 'user')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $trainers = User::query()
            ->where('user_type', 'trainer')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $gyms = User::query()
            ->where('user_type', 'gym')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $sessions = Classes::query()
            ->orderBy('session_title')
            ->get(['id', 'session_title', 'user_id']);

        return view('admin.bookings.index', compact(
            'bookings',
            'customers',
            'trainers',
            'gyms',
            'sessions'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'trainer', 'gym', 'session', 'payment']);

        return response()->json(['booking' => $booking]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'trainer_id' => $request->filled('trainer_id') ? $request->trainer_id : null,
            'gym_id' => $request->filled('gym_id') ? $request->gym_id : null,
            'payment_id' => $request->filled('payment_id') ? $request->payment_id : null,
        ]);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'trainer_id' => ['nullable', 'exists:users,id'],
            'gym_id' => ['nullable', 'exists:users,id'],
            'session_id' => ['required', 'exists:classes,id'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'booking_date' => ['required', 'date'],
            'time_slot' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['0', '1', '2'])],
            'payment_status' => ['required', Rule::in(['pending', 'paid', 'failed'])],
        ]);

        $validated = $this->normalizeNullableBookingFields($validated);

        if (! empty($validated['trainer_id'])) {
            $trainer = User::where('id', $validated['trainer_id'])->where('user_type', 'trainer')->first();
            if (! $trainer) {
                return response()->json(['message' => 'Invalid trainer.'], 422);
            }
        }
        if (! empty($validated['gym_id'])) {
            $gym = User::where('id', $validated['gym_id'])->where('user_type', 'gym')->first();
            if (! $gym) {
                return response()->json(['message' => 'Invalid gym.'], 422);
            }
        }

        $booking = Booking::create($validated);

        return response()->json([
            'message' => 'Booking created successfully.',
            'booking' => $booking->load(['user', 'trainer', 'gym', 'session', 'payment']),
        ], 201);
    }

    public function update(Request $request, Booking $booking)
    {
        $request->merge([
            'trainer_id' => $request->filled('trainer_id') ? $request->trainer_id : null,
            'gym_id' => $request->filled('gym_id') ? $request->gym_id : null,
            'payment_id' => $request->filled('payment_id') ? $request->payment_id : null,
        ]);

        $validated = $request->validate([
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'trainer_id' => ['nullable', 'exists:users,id'],
            'gym_id' => ['nullable', 'exists:users,id'],
            'session_id' => ['sometimes', 'required', 'exists:classes,id'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'booking_date' => ['sometimes', 'required', 'date'],
            'time_slot' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::in(['0', '1', '2'])],
            'payment_status' => ['sometimes', 'required', Rule::in(['pending', 'paid', 'failed'])],
        ]);

        $validated = $this->normalizeNullableBookingFields($validated);

        if (array_key_exists('trainer_id', $validated) && $validated['trainer_id']) {
            $trainer = User::where('id', $validated['trainer_id'])->where('user_type', 'trainer')->first();
            if (! $trainer) {
                return response()->json(['message' => 'Invalid trainer.'], 422);
            }
        }
        if (array_key_exists('gym_id', $validated) && $validated['gym_id']) {
            $gym = User::where('id', $validated['gym_id'])->where('user_type', 'gym')->first();
            if (! $gym) {
                return response()->json(['message' => 'Invalid gym.'], 422);
            }
        }

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => $booking->fresh()->load(['user', 'trainer', 'gym', 'session', 'payment']),
        ]);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizeNullableBookingFields(array $validated): array
    {
        foreach (['trainer_id', 'gym_id', 'payment_id'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] === '') {
                $validated[$key] = null;
            }
        }

        return $validated;
    }
}
