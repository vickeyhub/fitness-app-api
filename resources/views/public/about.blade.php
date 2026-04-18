@extends('layouts.public')

@section('title', 'About')

@section('content')
    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-16 md:py-24">
            <p class="fx-badge mb-4">Our story</p>
            <h1 class="max-w-3xl font-display text-4xl font-extrabold tracking-tight text-white md:text-5xl lg:text-6xl">
                Fitness tools that respect your time
            </h1>
            <p class="mt-6 max-w-2xl text-lg text-zinc-400">
                FitX connects people who train with coaches and venues that deliver real results. The platform is API-first so your profile, bookings, and payments stay consistent on web and mobile.
            </p>
        </div>
    </section>

    <section class="py-16 md:py-24">
        <div class="fx-container">
            <div class="grid gap-12 lg:grid-cols-2 lg:items-start">
                <div>
                    <h2 class="font-display text-2xl font-bold text-white md:text-3xl">What we believe</h2>
                    <ul class="mt-8 space-y-5">
                        @foreach ([
                            'Scheduling and payments should feel effortless.',
                            'Trainers and gyms deserve software that stays out of the way.',
                            'Security and clear rules beat flashy features.',
                        ] as $item)
                            <li class="flex gap-4 text-zinc-400">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-gradient-to-br from-teal-400 to-lime-300"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="fx-glass-strong rounded-3xl p-8 md:p-10">
                    <p class="text-sm font-semibold uppercase tracking-wider text-teal-400/90">For members</p>
                    <p class="mt-4 text-zinc-300">
                        Browse open sessions, compare options, then sign in when you are ready to book. Your account holds workouts, nutrition, and community features in one place.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('public.sessions') }}" class="fx-btn-primary text-sm">Browse sessions</a>
                        <a href="{{ route('public.contact') }}" class="fx-btn-secondary text-sm">Ask a question</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
