@extends('layouts.public')

@section('title', $session->session_title)

@section('content')
    @php
        $thumb = $session->session_thumbnail;
        $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') ? $thumb : asset('storage/'.ltrim($thumb, '/'))) : null;
    @endphp

    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-8 md:py-10">
            <nav class="text-sm text-zinc-500" aria-label="Breadcrumb">
                <a href="{{ route('public.sessions') }}" class="font-medium text-teal-400/90 hover:text-teal-300">Sessions</a>
                <span class="mx-2 text-zinc-700">/</span>
                <span class="text-zinc-400 line-clamp-1">{{ $session->session_title }}</span>
            </nav>
        </div>
    </section>

    <article class="fx-container py-12 md:py-16 lg:max-w-5xl">
        @if ($thumbUrl)
            <div class="overflow-hidden rounded-3xl border border-white/10 shadow-fx-card">
                <img src="{{ $thumbUrl }}" alt="" class="max-h-[460px] w-full object-cover" loading="lazy">
            </div>
        @endif

        <div class="mt-10 lg:mt-12">
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-white md:text-4xl lg:text-5xl">{{ $session->session_title }}</h1>
            @if ($session->user)
                <p class="mt-4 text-lg text-zinc-400">
                    With <span class="font-medium text-zinc-200">{{ $session->user->first_name }} {{ $session->user->last_name }}</span>
                    <span class="text-zinc-600">· {{ $session->user->user_type }}</span>
                </p>
            @endif
        </div>

        <dl class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @if ($session->price !== null)
                <div class="fx-glass rounded-2xl p-5">
                    <dt class="text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Price</dt>
                    <dd class="mt-1 font-display text-2xl font-bold text-teal-300">${{ number_format((float) $session->price, 2) }}</dd>
                </div>
            @endif
            @if ($session->duration)
                <div class="fx-glass rounded-2xl p-5">
                    <dt class="text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Duration</dt>
                    <dd class="mt-1 font-display text-2xl font-bold text-white">{{ $session->duration }} min</dd>
                </div>
            @endif
            @if ($session->calories)
                <div class="fx-glass rounded-2xl p-5">
                    <dt class="text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Calories (est.)</dt>
                    <dd class="mt-1 font-display text-2xl font-bold text-white">{{ $session->calories }}</dd>
                </div>
            @endif
            @if ($session->session_avrage_rating)
                <div class="fx-glass rounded-2xl p-5">
                    <dt class="text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Rating</dt>
                    <dd class="mt-1 font-display text-2xl font-bold text-white">{{ $session->session_avrage_rating }}</dd>
                </div>
            @endif
        </dl>

        @if (filled($session->session_timing))
            <div class="fx-glass-strong mt-8 rounded-2xl p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Time window</h2>
                <p class="mt-2 text-lg text-zinc-200">{{ $session->session_timing }}</p>
            </div>
        @endif

        @if ($session->schedule && (is_array($session->schedule) ? count($session->schedule) : filled($session->schedule)))
            <div class="fx-glass mt-6 rounded-2xl p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Weekly schedule</h2>
                <pre class="mt-3 overflow-x-auto whitespace-pre-wrap font-sans text-sm text-zinc-400">{{ is_array($session->schedule) ? json_encode($session->schedule, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $session->schedule }}</pre>
            </div>
        @endif

        @if ($session->intensity)
            <p class="mt-8 text-zinc-400"><span class="font-semibold text-zinc-300">Intensity</span> · {{ $session->intensity }}</p>
        @endif

        @if (filled($session->description))
            <div class="mt-10 border-t border-white/10 pt-10">
                <h2 class="font-display text-xl font-bold text-white">About this session</h2>
                <p class="mt-4 max-w-3xl leading-relaxed text-zinc-400">{{ $session->description }}</p>
            </div>
        @endif

        <div class="mt-12 flex flex-wrap gap-4 border-t border-white/10 pt-10">
            <a href="{{ route('auth.login') }}" class="fx-btn-primary px-8">Log in to book</a>
            <a href="{{ route('public.sessions') }}" class="fx-btn-secondary">All sessions</a>
        </div>
    </article>
@endsection
