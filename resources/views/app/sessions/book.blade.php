@extends('layouts.app')

@section('title', 'Book · '.$session->session_title)
@section('heading', 'Book session')

@section('content')
    <div class="mb-6">
        <a href="{{ route('app.sessions.show', $session->id) }}" class="text-sm font-medium text-teal-400 hover:text-teal-300">← {{ $session->session_title }}</a>
    </div>

    <div class="mx-auto max-w-lg">
        <div class="fx-glass-strong rounded-3xl p-6 sm:p-8">
            <p class="text-sm text-zinc-500">Session</p>
            <p class="font-display text-xl font-bold text-white">{{ $session->session_title }}</p>
            @if ($session->price !== null)
                <p class="mt-2 text-teal-300">${{ number_format((float) $session->price, 2) }}</p>
            @endif

            <form method="post" action="{{ route('app.sessions.bookings.store', $session->id) }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="fx-label" for="booking_date">Date</label>
                    <input class="fx-input" type="date" name="booking_date" id="booking_date" value="{{ old('booking_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                    @error('booking_date')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="time_slot">Time slot</label>
                    @if (count($timeSlotSuggestions) > 0)
                        <select class="fx-input mb-2" id="time_slot_preset" onchange="document.getElementById('time_slot').value = this.value">
                            <option value="">Choose a suggestion…</option>
                            @foreach ($timeSlotSuggestions as $s)
                                <option value="{{ $s }}" @selected(old('time_slot') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    @endif
                    <input class="fx-input" type="text" name="time_slot" id="time_slot" value="{{ old('time_slot') }}" placeholder="e.g. 07:00am - 08:30am" required>
                    @error('time_slot')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                    @error('booking')
                        <p class="mt-2 text-sm text-rose-400">{{ is_array($message) ? implode(' ', $message) : $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="fx-btn-primary w-full justify-center py-3">Confirm booking</button>
            </form>
        </div>
    </div>
@endsection
