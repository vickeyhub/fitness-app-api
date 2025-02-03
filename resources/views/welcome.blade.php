<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif
</head>

<body class="bg-gray-900 text-white">
    <header class="bg-gray-800 py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <h1 class="text-3xl font-bold">GymPro</h1>
            <nav>
                <a href="#" class="px-4 hover:text-yellow-400">Home</a>
                <a href="#membership" class="px-4 hover:text-yellow-400">Membership</a>
                <a href="#services" class="px-4 hover:text-yellow-400">Services</a>
                <a href="#gallery" class="px-4 hover:text-yellow-400">Gallery</a>
                <a href="#contact" class="px-4 hover:text-yellow-400">Contact</a>
            </nav>
        </div>
    </header>

    <section class="h-screen flex flex-col justify-center items-center text-center px-6 bg-cover bg-center"
        style="background-image: url('https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');">
        <h2 class="text-5xl font-bold text-yellow-400">Transform Your Body & Mind</h2>
        <p class="text-lg text-gray-300 mt-4">Join GymPro and start your fitness journey today.</p>
        <a href="/register"
            class="mt-6 bg-yellow-400 text-gray-900 px-6 py-3 rounded-full text-lg font-semibold hover:bg-yellow-300 transition">Get
            Started</a>
    </section>

    <section id="membership" class="container mx-auto py-16 px-6 text-center">
        <h2 class="text-4xl font-bold text-yellow-400">Membership Plans</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-yellow-400">Basic Plan</h3>
                <ul class="text-gray-300 mt-2 text-left list-disc list-inside">
                    <li>Access to gym equipment</li>
                    <li>Locker room access</li>
                    <li>Free Wi-Fi</li>
                </ul>
                <p class="text-yellow-400 text-xl mt-4">$20/month</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-yellow-400">Standard Plan</h3>
                <ul class="text-gray-300 mt-2 text-left list-disc list-inside">
                    <li>Includes Basic Plan benefits</li>
                    <li>Access to fitness classes</li>
                    <li>Personalized workout plan</li>
                </ul>
                <p class="text-yellow-400 text-xl mt-4">$35/month</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-yellow-400">Premium Plan</h3>
                <ul class="text-gray-300 mt-2 text-left list-disc list-inside">
                    <li>Includes Standard Plan benefits</li>
                    <li>Personal trainer sessions</li>
                    <li>Customized diet plan</li>
                    <li>Sauna & spa access</li>
                </ul>
                <p class="text-yellow-400 text-xl mt-4">$50/month</p>
            </div>
        </div>
    </section>

    <section class="container mx-auto py-16 px-6 text-center">
        <h2 class="text-4xl font-bold text-yellow-400">Gallery</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <img src="https://images.pexels.com/photos/1229356/pexels-photo-1229356.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                class="rounded-lg shadow-lg">
            <img src="https://images.pexels.com/photos/3253501/pexels-photo-3253501.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                class="rounded-lg shadow-lg">

            <img src="https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                class="rounded-lg shadow-lg">
            <img src="https://images.pexels.com/photos/1954524/pexels-photo-1954524.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                class="rounded-lg shadow-lg">
        </div>
    </section>

    <footer class="bg-gray-800 text-center py-6 mt-12">
        <p class="text-gray-300">&copy; 2025 GymPro. All Rights Reserved.</p>
        <div class="flex justify-center mt-4">
            <a href="#" class="mx-2 hover:text-yellow-400">Privacy Policy</a>
            <a href="#" class="mx-2 hover:text-yellow-400">Terms of Service</a>
        </div>
    </footer>
</body>

</html>
