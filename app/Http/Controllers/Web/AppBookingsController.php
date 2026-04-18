<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppBookingsController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab', 'upcoming')->toString();
        if (! in_array($tab, ['upcoming', 'past', 'cancelled'], true)) {
            $tab = 'upcoming';
        }

        $user = $request->user();
        $query = Booking::query()
            ->with([
                'trainer:id,first_name,last_name,user_type',
                'gym:id,first_name,last_name,user_type',
                'session:id,session_title,duration,price,session_thumbnail,session_timing',
                'payment:id,payment_intent_id,amount,currency,status',
            ]);

        if ($user->user_type === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->user_type === 'trainer') {
            $query->where('trainer_id', $user->id);
        } elseif ($user->user_type === 'gym') {
            $query->where('gym_id', $user->id);
        }

        $today = Carbon::today();

        $query->when($tab === 'cancelled', fn ($q) => $q->where('status', '0'))
            ->when($tab === 'upcoming', function ($q) use ($today) {
                $q->where('status', '!=', '0')
                    ->whereDate('booking_date', '>=', $today);
            })
            ->when($tab === 'past', function ($q) use ($today) {
                $q->where('status', '!=', '0')
                    ->whereDate('booking_date', '<', $today);
            });

        $bookings = $query->orderByDesc('booking_date')->orderByDesc('id')->get();

        return view('app.bookings.index', [
            'bookings' => $bookings,
            'tab' => $tab,
        ]);
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeBooking($request, $booking);

        $booking->load([
            'trainer:id,first_name,last_name,email,mobile_number,user_type',
            'gym:id,first_name,last_name,email,mobile_number,user_type',
            'user:id,first_name,last_name,email',
            'session',
            'payment:id,payment_intent_id,amount,currency,status,email,name',
        ]);

        return view('app.bookings.show', ['booking' => $booking]);
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        $user = $request->user();
        $ok = match ($user->user_type) {
            'user' => (int) $booking->user_id === (int) $user->id,
            'trainer' => (int) $booking->trainer_id === (int) $user->id,
            'gym' => (int) $booking->gym_id === (int) $user->id,
            default => false,
        };

        abort_unless($ok, 403);
    }
}
