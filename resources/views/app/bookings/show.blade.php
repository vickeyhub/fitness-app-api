@extends('layouts.app')

@section('title', 'Booking #'.$booking->id)
@section('heading', 'Booking details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('app.bookings.index') }}" class="text-sm font-medium text-teal-400 hover:text-teal-300">← My bookings</a>
    </div>

    <div class="mx-auto max-w-2xl space-y-6">
        <div class="fx-glass-strong rounded-3xl p-6 sm:p-8">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-[11px] font-semibold uppercase text-zinc-600">Booking ID</dt>
                    <dd class="mt-1 font-mono text-white">#{{ $booking->id }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold uppercase text-zinc-600">Date</dt>
                    <dd class="mt-1 text-white">{{ $booking->booking_date }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-semibold uppercase text-zinc-600">Time slot</dt>
                    <dd class="mt-1 text-white">{{ $booking->time_slot }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold uppercase text-zinc-600">Booking status</dt>
                    <dd class="mt-1 text-zinc-300">
                        @if ($booking->status === '0') Cancelled
                        @elseif ($booking->status === '2') Pending
                        @else Confirmed
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold uppercase text-zinc-600">Payment</dt>
                    <dd class="mt-1 text-zinc-300">{{ $booking->payment_status }}</dd>
                </div>
            </dl>
        </div>

        @if ($booking->session)
            <div class="fx-glass rounded-2xl p-6">
                <h3 class="font-display text-lg font-bold text-white">Session</h3>
                <p class="mt-2 text-zinc-300">{{ $booking->session->session_title }}</p>
                @if ($booking->session->price !== null)
                    <p class="mt-2 text-teal-300">${{ number_format((float) $booking->session->price, 2) }}</p>
                @endif
                <a href="{{ route('app.sessions.show', $booking->session_id) }}" class="mt-4 inline-block text-sm font-medium text-teal-400 hover:underline">View session</a>
            </div>
        @endif

        @if ($booking->payment)
            <div class="fx-glass rounded-2xl p-6">
                <h3 class="font-display text-lg font-bold text-white">Payment record</h3>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-zinc-600">Intent</dt><dd class="font-mono text-xs text-zinc-400">{{ $booking->payment->payment_intent_id }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-600">Status</dt><dd class="text-zinc-300">{{ $booking->payment->status }}</dd></div>
                    @if ($booking->payment->amount)
                        <div class="flex justify-between gap-4"><dt class="text-zinc-600">Amount</dt><dd class="text-zinc-300">{{ $booking->payment->amount }} {{ strtoupper($booking->payment->currency ?? '') }}</dd></div>
                    @endif
                </dl>
            </div>
        @endif

        <div class="fx-glass rounded-2xl p-6 text-sm text-zinc-500">
            <p>Web checkout with Stripe will attach payment to this booking. For now, pending bookings await payment confirmation from the app flow.</p>
        </div>
    </div>
@endsection
