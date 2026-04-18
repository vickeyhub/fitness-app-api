@extends('layouts.app')

@section('title', $session->session_title)
@section('heading', $session->session_title)

@section('content')
    @php
        $thumb = $session->session_thumbnail;
        $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') ? $thumb : asset('storage/'.ltrim($thumb, '/'))) : null;
    @endphp

    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('app.sessions.index') }}" class="text-sm font-medium text-teal-400 hover:text-teal-300">← All sessions</a>
    </div>

    @if ($thumbUrl)
        <div class="mb-8 overflow-hidden rounded-3xl border border-white/10">
            <img src="{{ $thumbUrl }}" alt="" class="max-h-[380px] w-full object-cover">
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            @if ($session->user)
                <p class="text-sm text-zinc-500">
                    Host:
                    <span class="font-medium text-zinc-300">{{ $session->user->first_name }} {{ $session->user->last_name }}</span>
                    @if ($session->user->email)
                        <span class="text-zinc-600">· {{ $session->user->email }}</span>
                    @endif
                </p>
            @endif

            <dl class="grid gap-4 sm:grid-cols-2">
                @if ($session->price !== null)
                    <div class="fx-glass rounded-2xl p-4">
                        <dt class="text-[11px] font-semibold uppercase text-zinc-600">Price</dt>
                        <dd class="mt-1 font-display text-xl font-bold text-teal-300">${{ number_format((float) $session->price, 2) }}</dd>
                    </div>
                @endif
                @if ($session->duration)
                    <div class="fx-glass rounded-2xl p-4">
                        <dt class="text-[11px] font-semibold uppercase text-zinc-600">Duration</dt>
                        <dd class="mt-1 font-display text-xl font-bold text-white">{{ $session->duration }} min</dd>
                    </div>
                @endif
                @if ($session->session_avrage_rating)
                    <div class="fx-glass rounded-2xl p-4">
                        <dt class="text-[11px] font-semibold uppercase text-zinc-600">Rating</dt>
                        <dd class="mt-1 font-display text-xl font-bold text-white">{{ $session->session_avrage_rating }}</dd>
                    </div>
                @endif
            </dl>

            @if (filled($session->session_timing))
                <div class="fx-glass rounded-2xl p-5">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Typical time</h3>
                    <p class="mt-2 text-zinc-200">{{ $session->session_timing }}</p>
                </div>
            @endif

            @if (filled($session->description))
                <div>
                    <h3 class="font-display text-lg font-bold text-white">About</h3>
                    <p class="mt-3 leading-relaxed text-zinc-400">{{ $session->description }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="fx-glass-strong rounded-2xl p-5">
                <form method="post" action="{{ route('app.sessions.bookmark', $session->id) }}" class="mb-4">
                    @csrf
                    <button type="submit" class="fx-btn-secondary w-full justify-center text-sm">
                        {{ $isBookmarked ? 'Remove bookmark' : 'Bookmark' }}
                    </button>
                </form>
                @error('bookmark')
                    <p class="mb-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
                <a href="{{ route('app.sessions.book', $session->id) }}" class="fx-btn-primary flex w-full justify-center py-3 text-center">Book this session</a>
                <p class="mt-3 text-xs text-zinc-600">Paid sessions stay pending until checkout (Stripe) is wired for web.</p>
            </div>
        </div>
    </div>
@endsection
