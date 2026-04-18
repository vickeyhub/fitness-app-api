@extends('layouts.public')

@section('title', 'Contact')

@section('content')
    <section class="relative border-b border-white/[0.06] fx-hero-mesh">
        <div class="fx-container py-16 md:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <p class="fx-badge mb-4">We reply to everyone</p>
                <h1 class="fx-section-title">Let’s talk</h1>
                <p class="fx-prose mt-4">
                    Booking or billing question? Include the email on your account so we can help without back-and-forth.
                </p>
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24">
        <div class="fx-container">
            <div class="mx-auto grid max-w-5xl gap-12 lg:grid-cols-5 lg:gap-16">
                <div class="lg:col-span-2">
                    <h2 class="font-display text-xl font-bold text-white">Direct lines</h2>
                    <p class="mt-4 text-sm text-zinc-500">
                        Prefer email? Use the form — we log inquiries securely and respond in order received.
                    </p>
                    <dl class="mt-10 space-y-6 text-sm">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Response time</dt>
                            <dd class="mt-1 text-zinc-300">Typically within 1–2 business days</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Before you write</dt>
                            <dd class="mt-1 text-zinc-400">Check <a href="{{ route('public.pricing') }}" class="text-teal-400 hover:underline">pricing</a> and <a href="{{ route('public.sessions') }}" class="text-teal-400 hover:underline">sessions</a> — many answers live there.</dd>
                        </div>
                    </dl>
                </div>
                <div class="lg:col-span-3">
                    <div class="fx-glass-strong rounded-3xl p-6 sm:p-8 md:p-10">
                        @if (session('success'))
                            <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="post" action="{{ route('public.contact.submit') }}" class="space-y-6">
                            @csrf
                            <div>
                                <label class="fx-label" for="name">Name</label>
                                <input class="fx-input" type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name">
                                @error('name')
                                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="fx-label" for="email">Email</label>
                                <input class="fx-input" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="fx-label" for="message">Message</label>
                                <textarea class="fx-input min-h-[160px] resize-y" name="message" id="message" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="fx-btn-primary w-full justify-center py-3.5 text-base">Send message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
