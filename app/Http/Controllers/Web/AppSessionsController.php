<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\BookingsController as ApiBookingsController;
use App\Http\Controllers\Api\SessionsController as ApiSessionsController;
use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Classes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppSessionsController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim();
        $sort = $request->string('sort', 'newest')->toString();

        $query = Classes::query()
            ->where('is_publish', '1')
            ->with(['user:id,first_name,last_name,user_type'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('session_title', 'LIKE', '%'.$q.'%');
            });

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'title' => $query->orderBy('session_title'),
            default => $query->orderByDesc('id'),
        };

        $sessions = $query->paginate(12)->withQueryString();

        return view('app.sessions.index', [
            'sessions' => $sessions,
            'q' => $q,
            'sort' => $sort,
        ]);
    }

    public function show(Request $request, int $session): View
    {
        $model = Classes::query()
            ->where('is_publish', '1')
            ->where('id', $session)
            ->with(['user:id,first_name,last_name,user_type,email,mobile_number'])
            ->firstOrFail();

        $isBookmarked = Bookmark::query()
            ->where('user_id', $request->user()->id)
            ->where('session_id', $model->id)
            ->exists();

        return view('app.sessions.show', [
            'session' => $model,
            'isBookmarked' => $isBookmarked,
            'timeSlotSuggestions' => $this->suggestTimeSlots($model),
        ]);
    }

    public function book(Request $request, int $session): View
    {
        $model = Classes::query()
            ->where('is_publish', '1')
            ->where('id', $session)
            ->with(['user:id,first_name,last_name,user_type'])
            ->firstOrFail();

        return view('app.sessions.book', [
            'session' => $model,
            'timeSlotSuggestions' => $this->suggestTimeSlots($model),
        ]);
    }

    public function storeBooking(Request $request, int $session): RedirectResponse
    {
        $model = Classes::query()
            ->where('is_publish', '1')
            ->where('id', $session)
            ->with('user')
            ->firstOrFail();

        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string|max:255',
        ]);

        $owner = $model->user;
        $trainerId = null;
        $gymId = null;
        if ($owner) {
            if ($owner->user_type === 'trainer') {
                $trainerId = $owner->id;
            }
            if ($owner->user_type === 'gym') {
                $gymId = $owner->id;
            }
        }

        $price = (float) ($model->price ?? 0);
        $isFree = $price <= 0;

        $request->merge([
            'session_id' => (string) $model->id,
            'trainer' => $trainerId,
            'gym' => $gymId,
            'status' => $isFree ? '1' : '2',
            'payment_status' => $isFree ? 'paid' : 'pending',
            'payment_id' => null,
        ]);

        $response = app(ApiBookingsController::class)->createBookings($request);
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 201) {
            if ($response->getStatusCode() === 422 && ! empty($payload['errors'])) {
                return back()->withErrors(['booking' => $payload['errors']])->withInput();
            }

            return back()->withErrors([
                'booking' => $payload['message'] ?? __('Could not create booking.'),
            ])->withInput();
        }

        $booking = $payload['booking'] ?? null;
        $id = is_array($booking) ? ($booking['id'] ?? null) : ($booking?->id ?? null);

        if ($id) {
            return redirect()
                ->route('app.bookings.show', $id)
                ->with('status', __('Booking saved.'));
        }

        return redirect()->route('app.bookings.index')->with('status', __('Booking saved.'));
    }

    public function toggleBookmark(Request $request, int $session): RedirectResponse
    {
        $request->merge(['session_id' => $session]);

        $response = app(ApiSessionsController::class)->save_bookmark($request);
        $payload = json_decode($response->getContent(), true);

        if ($response->getStatusCode() >= 400) {
            return back()->withErrors([
                'bookmark' => $payload['message'] ?? __('Could not update bookmark.'),
            ]);
        }

        $msg = $payload['message'] ?? __('Updated.');

        return back()->with('status', $msg);
    }

    /**
     * @return list<string>
     */
    private function suggestTimeSlots(Classes $session): array
    {
        $slots = [];
        if (filled($session->session_timing)) {
            $slots[] = trim((string) $session->session_timing);
        }
        if (is_array($session->schedule)) {
            foreach ($session->schedule as $item) {
                if (is_string($item)) {
                    $slots[] = $item;
                } elseif (is_array($item)) {
                    $slots[] = json_encode($item);
                }
            }
        }

        return array_values(array_unique(array_filter($slots)));
    }
}
