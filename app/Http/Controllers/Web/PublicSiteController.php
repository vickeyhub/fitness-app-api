<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        return view('public.home');
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function pricing(): View
    {
        return view('public.pricing');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Future: dispatch notification / store inquiry. For now acknowledge receipt only.
        logger()->info('public_contact_form', [
            'email' => $validated['email'],
            'name' => $validated['name'],
        ]);

        return back()->with('success', 'Thanks — we received your message and will get back to you soon.');
    }

    /**
     * Public-safe session listing (published sessions only).
     */
    public function sessions(Request $request): View
    {
        $q = $request->string('q')->trim();

        $sessions = Classes::query()
            ->where('is_publish', '1')
            ->when($q->isNotEmpty(), function ($query) use ($q) {
                $query->where('session_title', 'LIKE', '%'.$q.'%');
            })
            ->with(['user:id,first_name,last_name,user_type'])
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('public.sessions.index', [
            'sessions' => $sessions,
            'q' => (string) $q,
        ]);
    }

    /**
     * Public-safe session detail (published only).
     */
    public function sessionShow(int $id): View
    {
        $session = Classes::query()
            ->where('is_publish', '1')
            ->where('id', $id)
            ->with(['user:id,first_name,last_name,user_type'])
            ->firstOrFail();

        return view('public.sessions.show', ['session' => $session]);
    }

    public function trainers(Request $request): View
    {
        $q = $request->string('q')->trim();

        $trainers = User::query()
            ->select('id', 'first_name', 'last_name')
            ->where('user_type', 'trainer')
            ->where('status', '1')
            ->when($q->isNotEmpty(), function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('first_name', 'LIKE', '%'.$q.'%')
                        ->orWhere('last_name', 'LIKE', '%'.$q.'%');
                });
            })
            ->with([
                'profile:id,user_id,specialties,rating,location,experience_level,profile_picture,user_description',
            ])
            ->orderBy('first_name')
            ->paginate(12)
            ->withQueryString();

        return view('public.trainers.index', [
            'trainers' => $trainers,
            'q' => (string) $q,
        ]);
    }

    public function trainerShow(int $id): View
    {
        $trainer = User::query()
            ->where('user_type', 'trainer')
            ->where('status', '1')
            ->where('id', $id)
            ->with([
                'profile:id,user_id,specialties,rating,location,experience_level,profile_picture,user_description,trainer_services,gender',
            ])
            ->firstOrFail();

        return view('public.trainers.show', ['trainer' => $trainer]);
    }

    public function gyms(Request $request): View
    {
        $q = $request->string('q')->trim();
        $location = $request->string('location')->trim();

        $gyms = User::query()
            ->select('id', 'first_name', 'last_name')
            ->where('user_type', 'gym')
            ->where('status', '1')
            ->when($q->isNotEmpty(), function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('first_name', 'LIKE', '%'.$q.'%')
                        ->orWhere('last_name', 'LIKE', '%'.$q.'%');
                });
            })
            ->when($location->isNotEmpty(), function ($query) use ($location) {
                $query->whereHas('profile', fn ($p) => $p->where('location', 'LIKE', '%'.$location.'%'));
            })
            ->with(['profile:id,user_id,specialties,rating,location,profile_picture,user_description'])
            ->orderBy('first_name')
            ->paginate(12)
            ->withQueryString();

        return view('public.gyms.index', [
            'gyms' => $gyms,
            'q' => (string) $q,
            'location' => (string) $location,
        ]);
    }

    public function gymShow(int $id): View
    {
        $gym = User::query()
            ->where('user_type', 'gym')
            ->where('status', '1')
            ->where('id', $id)
            ->with([
                'profile:id,user_id,specialties,rating,location,profile_picture,user_description',
            ])
            ->firstOrFail();

        return view('public.gyms.show', ['gym' => $gym]);
    }
}
