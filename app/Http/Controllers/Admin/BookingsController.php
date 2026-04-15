<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Classes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 20);
        if (! in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 20;
        }

        $bookings = Booking::with(['user', 'trainer', 'gym', 'session', 'payment'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($term) {
                    $inner->where('time_slot', 'like', "%{$term}%")
                        ->orWhere('payment_id', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', (int) $request->integer('user_id'));
            })
            ->when($request->filled('session_id'), function ($query) use ($request) {
                $query->where('session_id', (int) $request->integer('session_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', (string) $request->string('status'));
            })
            ->when($request->filled('payment_status'), function ($query) use ($request) {
                $query->where('payment_status', (string) $request->string('payment_status'));
            })
            ->when($request->filled('booking_from'), function ($query) use ($request) {
                $query->whereDate('booking_date', '>=', (string) $request->string('booking_from'));
            })
            ->when($request->filled('booking_to'), function ($query) use ($request) {
                $query->whereDate('booking_date', '<=', (string) $request->string('booking_to'));
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

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
            'sessions',
            'perPage'
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
            'time_slot' => $this->buildTimeSlot($request->input('start_time'), $request->input('end_time')),
        ]);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'trainer_id' => ['nullable', 'exists:users,id'],
            'gym_id' => ['nullable', 'exists:users,id'],
            'session_id' => ['required', 'exists:classes,id'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:20'],
            'end_time' => ['required', 'string', 'max:20'],
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
            'time_slot' => ($request->filled('start_time') || $request->filled('end_time'))
                ? $this->buildTimeSlot($request->input('start_time'), $request->input('end_time'))
                : $request->input('time_slot'),
        ]);

        $validated = $request->validate([
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'trainer_id' => ['nullable', 'exists:users,id'],
            'gym_id' => ['nullable', 'exists:users,id'],
            'session_id' => ['sometimes', 'required', 'exists:classes,id'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'booking_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['sometimes', 'required', 'string', 'max:20'],
            'end_time' => ['sometimes', 'required', 'string', 'max:20'],
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

    private function buildTimeSlot(?string $start, ?string $end): string
    {
        $startParsed = $this->normalizeTime($start ?? '');
        $endParsed = $this->normalizeTime($end ?? '');

        if ($startParsed === null || $endParsed === null) {
            throw ValidationException::withMessages([
                'time_slot' => ['Start time and end time are required in format like 10:00am and 11:00am.'],
            ]);
        }

        return $startParsed . ' - ' . $endParsed;
    }

    private function normalizeTime(string $value): ?string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return null;
        }

        foreach (['h:ia', 'h:i a', 'H:i'] as $format) {
            try {
                $time = Carbon::createFromFormat($format, $value);

                return strtolower($time->format('h:ia'));
            } catch (\Throwable) {
                // Try next format.
            }
        }

        return null;
    }
}
