<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitX | Train Smarter</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-base-200 text-base-content" data-theme="dark" id="theme">
    <header class="navbar sticky top-0 z-50 border-b border-base-300/70 bg-base-100/90 px-6 backdrop-blur">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between py-2">
            <a href="/" class="text-xl font-extrabold tracking-tight">
                Fit<span class="text-amber-400">X</span>
            </a>
            <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 md:flex">
                <a href="#programs" class="transition hover:text-amber-400">Programs</a>
                <a href="#schedule" class="transition hover:text-amber-400">Schedule</a>
                <a href="#pricing" class="transition hover:text-amber-400">Pricing</a>
                <a href="#testimonials" class="transition hover:text-amber-400">Testimonials</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('web-login') }}" class="btn btn-outline btn-sm">
                    Log in
                </a>
                <button id="themeToggle" class="btn btn-ghost btn-sm" type="button" aria-label="Toggle theme">
                    Light theme
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/25 via-transparent to-cyan-400/10"></div>
            <div class="mx-auto grid max-w-6xl gap-10 px-6 pb-24 pt-20 md:grid-cols-2 md:items-center">
                <div class="relative z-10">
                    <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-amber-300">
                        Premium Fitness Club
                    </p>
                    <h1 class="text-4xl font-extrabold leading-tight text-white md:text-6xl">
                        Build strength, energy, and confidence.
                    </h1>
                    <p class="mt-5 max-w-xl text-base text-slate-300 md:text-lg">
                        Personalized coaching, science-backed programs, and a supportive community that keeps you consistent.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#pricing" class="btn btn-warning">
                            Start Membership
                        </a>
                        <a href="#programs" class="btn btn-outline">
                            Explore Programs
                        </a>
                    </div>
                    <div class="mt-10 grid grid-cols-3 gap-5 text-center">
                        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4">
                            <p class="text-2xl font-extrabold text-white">2K+</p>
                            <p class="text-xs text-slate-400">Active members</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4">
                            <p class="text-2xl font-extrabold text-white">40+</p>
                            <p class="text-xs text-slate-400">Weekly classes</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4">
                            <p class="text-2xl font-extrabold text-white">12</p>
                            <p class="text-xs text-slate-400">Expert coaches</p>
                        </div>
                    </div>
                </div>
                <div class="relative z-10">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-2xl shadow-cyan-900/20">
                        <img src="{{ asset('images/hero.jpg') }}" alt="Gym training area" class="h-[440px] w-full rounded-xl object-cover">
                    </div>
                </div>
            </div>
        </section>

        <section id="programs" class="mx-auto max-w-6xl px-6 py-20">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-white md:text-4xl">Programs for every goal</h2>
                <p class="mt-3 text-slate-400">Choose a training style that fits your pace and objective.</p>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                    <h3 class="text-xl font-bold text-white">Strength Lab</h3>
                    <p class="mt-3 text-sm text-slate-400">Progressive lifting plans focused on performance and muscle growth.</p>
                </article>
                <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                    <h3 class="text-xl font-bold text-white">HIIT Burn</h3>
                    <p class="mt-3 text-sm text-slate-400">High-energy interval sessions designed to maximize calorie burn.</p>
                </article>
                <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                    <h3 class="text-xl font-bold text-white">Mobility Flow</h3>
                    <p class="mt-3 text-sm text-slate-400">Improve flexibility, recovery, and movement quality with guided flow work.</p>
                </article>
            </div>
        </section>

        <section id="schedule" class="border-y border-slate-800 bg-slate-900/50">
            <div class="mx-auto max-w-6xl px-6 py-20">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-white md:text-4xl">Weekly class snapshot</h2>
                    <p class="mt-3 text-slate-400">Plan your week in advance.</p>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-800">
                    <table class="min-w-full divide-y divide-slate-800 text-left">
                        <thead class="bg-slate-900">
                            <tr class="text-xs uppercase tracking-wide text-slate-400">
                                <th class="px-6 py-4">Day</th>
                                <th class="px-6 py-4">Morning</th>
                                <th class="px-6 py-4">Evening</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-950/60 text-sm text-slate-200">
                            <tr>
                                <td class="px-6 py-4 font-semibold">Monday</td>
                                <td class="px-6 py-4">Strength Foundations</td>
                                <td class="px-6 py-4">HIIT Burn</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-semibold">Wednesday</td>
                                <td class="px-6 py-4">Mobility Flow</td>
                                <td class="px-6 py-4">Upper Body Power</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-semibold">Friday</td>
                                <td class="px-6 py-4">Core Conditioning</td>
                                <td class="px-6 py-4">Total Body Circuit</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="pricing" class="mx-auto max-w-6xl px-6 py-20">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-white md:text-4xl">Simple, flexible pricing</h2>
                <p class="mt-3 text-slate-400">No hidden fees. Cancel anytime.</p>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6 text-center">
                    <h3 class="text-lg font-semibold text-white">Starter</h3>
                    <p class="mt-4 text-4xl font-extrabold text-white">$29<span class="text-base font-medium text-slate-400">/mo</span></p>
                    <p class="mt-4 text-sm text-slate-400">Gym floor access and basic group classes.</p>
                </article>
                <article class="rounded-2xl border border-amber-400/60 bg-slate-900 p-6 text-center shadow-lg shadow-amber-700/10">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-300">Most Popular</p>
                    <h3 class="mt-2 text-lg font-semibold text-white">Pro</h3>
                    <p class="mt-4 text-4xl font-extrabold text-white">$59<span class="text-base font-medium text-slate-400">/mo</span></p>
                    <p class="mt-4 text-sm text-slate-400">Unlimited classes, one coaching session per month.</p>
                </article>
                <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6 text-center">
                    <h3 class="text-lg font-semibold text-white">Elite</h3>
                    <p class="mt-4 text-4xl font-extrabold text-white">$99<span class="text-base font-medium text-slate-400">/mo</span></p>
                    <p class="mt-4 text-sm text-slate-400">Personalized programming and weekly coach check-ins.</p>
                </article>
            </div>
        </section>

        <section id="testimonials" class="border-t border-slate-800 bg-slate-900/50">
            <div class="mx-auto max-w-6xl px-6 py-20">
                <h2 class="text-center text-3xl font-bold text-white md:text-4xl">What members say</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-2">
                    <blockquote class="rounded-2xl border border-slate-800 bg-slate-900 p-6 text-slate-300">
                        "The structure and coaching here completely changed my routine. I finally train consistently."
                        <footer class="mt-4 text-sm font-semibold text-white">- Priya K.</footer>
                    </blockquote>
                    <blockquote class="rounded-2xl border border-slate-800 bg-slate-900 p-6 text-slate-300">
                        "Programs are clear, motivating, and practical. I feel stronger and more energized every week."
                        <footer class="mt-4 text-sm font-semibold text-white">- Rohan M.</footer>
                    </blockquote>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 py-20">
            <div class="rounded-3xl border border-slate-800 bg-gradient-to-r from-amber-500/20 to-cyan-400/10 p-10 text-center">
                <h2 class="text-3xl font-bold text-white md:text-4xl">Ready to start your transformation?</h2>
                <p class="mx-auto mt-4 max-w-2xl text-slate-300">Book your free first session and get a personalized roadmap from our coaching team.</p>
                <a href="{{ route('web-login') }}" class="btn btn-warning mt-8">
                    Book Free Session
                </a>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-800 py-8 text-center text-sm text-slate-400">
        <p>&copy; {{ date('Y') }} FitX Gym. All rights reserved.</p>
    </footer>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const body = document.getElementById('theme');

        themeToggle.addEventListener('click', () => {
            const isDark = body.getAttribute('data-theme') === 'dark';
            body.setAttribute('data-theme', isDark ? 'light' : 'dark');
            themeToggle.textContent = isDark ? 'Dark theme' : 'Light theme';
        });
    </script>
</body>
</html>
