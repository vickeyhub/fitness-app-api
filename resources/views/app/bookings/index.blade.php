@extends('layouts.app')

@section('title', 'My bookings')
@section('heading', 'My bookings')

@section('content')
    <div class="mb-8 flex flex-wrap gap-2 border-b border-white/10 pb-4">
        @foreach (['upcoming' => 'Upcoming', 'past' => 'Past', 'cancelled' => 'Cancelled'] as $key => $label)
            <a
                href="{{ route('app.bookings.index', ['tab' => $key]) }}"
                class="rounded-full px-4 py-2 text-sm font-medium transition {{ $tab === $key ? 'bg-teal-500/20 text-teal-200' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}"
            >{{ $label }}</a>
        @endforeach
    </div>

    @if ($bookings->isEmpty())
        <div class="fx-glass rounded-3xl px-8 py-16 text-center text-zinc-500">
            <p>No bookings in this tab.</p>
            <a href="{{ route('app.sessions.index') }}" class="mt-4 inline-block text-teal-400 hover:underline">Browse sessions</a>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-white/[0.08]">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-white/10 bg-white/[0.03] text-xs uppercase tracking-wider text-zinc-500">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Session</th>
                        <th class="px-4 py-3">Slot</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @foreach ($bookings as $b)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-4 py-3 text-zinc-300">{{ $b->booking_date }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $b->session->session_title ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $b->time_slot }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full border border-white/10 px-2 py-0.5 text-xs text-zinc-400">
                                    @if ($b->status === '0') Cancelled
                                    @elseif ($b->status === '2') Pending
                                    @else Confirmed
                                    @endif
                                    · {{ $b->payment_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('app.bookings.show', $b) }}" class="text-teal-400 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
