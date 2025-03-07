<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Membership | FitX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 1s ease-out;
        }
    </style>
</head>
<body class="bg-gray-900 text-white transition-all duration-500" id="theme">

    <!-- Navigation -->
    <header class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-yellow-400">FitX</h1>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="#about" class="hover:text-yellow-400">About</a></li>
                    <li><a href="#features" class="hover:text-yellow-400">Features</a></li>
                    <li><a href="#gallery" class="hover:text-yellow-400">Gallery</a></li>
                    <li><a href="#plans" class="hover:text-yellow-400">Plans</a></li>
                    <li><a href="#contact" class="hover:text-yellow-400">Contact</a></li>
                </ul>
            </nav>
            <button id="themeToggle" class="ml-4 bg-yellow-500 px-4 py-2 rounded-lg text-gray-900 font-semibold">🌙 Dark</button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative h-screen flex items-center justify-center bg-cover bg-center fade-in" style="background-image: url('{{asset('images/hero.jpg')}}');">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="relative text-center">
            <h2 class="text-5xl font-extrabold">Elevate Your Fitness Journey</h2>
            <p class="text-lg text-gray-300 mt-4">Join our community and achieve your dream body.</p>
            <a href="#" class="mt-6 inline-block bg-yellow-500 px-6 py-3 rounded-lg text-gray-900 font-bold text-lg hover:bg-yellow-400 transition">Get Started</a>
        </div>
    </section>

    <!-- About Us -->
    <section id="about" class="container mx-auto py-16 px-6 text-center fade-in">
        <h3 class="text-3xl font-bold">About <span class="text-yellow-400">FitX</span></h3>
        <p class="mt-4 text-gray-400 max-w-2xl mx-auto">
            FitX is a modern fitness center designed to help you reach your goals. Our expert trainers, state-of-the-art equipment, and engaging group classes make us the perfect gym for all fitness levels.
        </p>
    </section>

    <!-- Gallery -->
    <section id="gallery" class="container mx-auto py-16 px-6 fade-in">
        <h3 class="text-3xl font-bold text-center">Our <span class="text-yellow-400">Gallery</span></h3>
        <div class="grid md:grid-cols-3 gap-8 mt-10">
            <img src="https://source.unsplash.com/500x400/?gym" class="rounded-lg shadow-lg" />
            <img src="https://source.unsplash.com/500x400/?workout" class="rounded-lg shadow-lg" />
            <img src="https://source.unsplash.com/500x400/?fitness" class="rounded-lg shadow-lg" />
        </div>
    </section>

    <!-- Membership Plans -->
    <section id="plans" class="container mx-auto py-16 px-6 fade-in">
        <h3 class="text-3xl font-bold text-center">Choose Your Plan</h3>
        <div class="grid md:grid-cols-3 gap-8 mt-10">
            <div class="bg-gray-800 p-6 rounded-lg text-center">
                <h4 class="text-xl font-semibold">Basic</h4>
                <p class="text-gray-400">Access to gym equipment</p>
                <p class="text-3xl font-bold mt-4">$20/mo</p>
                <a href="#" class="mt-4 block bg-yellow-500 px-4 py-2 rounded-lg text-gray-900 font-semibold">Join Now</a>
            </div>
        
            <div class="bg-gray-800 p-6 rounded-lg text-center">
                <h4 class="text-xl font-semibold">Medium</h4>
                <p class="text-gray-400">Access to gym equipment</p>
                <p class="text-3xl font-bold mt-4">$50/mo</p>
                <a href="#" class="mt-4 block bg-yellow-500 px-4 py-2 rounded-lg text-gray-900 font-semibold">Join Now</a>
            </div>
        
            <div class="bg-gray-800 p-6 rounded-lg text-center">
                <h4 class="text-xl font-semibold">Advanced</h4>
                <p class="text-gray-400">Access to gym equipment</p>
                <p class="text-3xl font-bold mt-4">$99/mo</p>
                <a href="#" class="mt-4 block bg-yellow-500 px-4 py-2 rounded-lg text-gray-900 font-semibold">Join Now</a>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section id="contact" class="container mx-auto py-16 px-6 text-center fade-in">
        <h3 class="text-3xl font-bold">Contact Us</h3>
        <form class="mt-6 max-w-lg mx-auto">
            <input type="text" placeholder="Your Name" class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600 mb-4">
            <input type="email" placeholder="Your Email" class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600 mb-4">
            <textarea placeholder="Your Message" class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600 mb-4"></textarea>
            <button type="submit" class="bg-yellow-500 px-6 py-3 rounded-lg text-gray-900 font-bold">Send Message</button>
        </form>
    </section>

    <!-- Live Chat Button -->
    <div class="fixed bottom-6 right-6">
        <a href="https://wa.me/1234567890" target="_blank" class="bg-green-500 text-white p-3 rounded-full shadow-lg hover:bg-green-400 transition">
            💬 Chat with Us
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 py-6 text-center">
        <p class="text-gray-400">&copy; 2025 FitX Gym. All rights reserved.</p>
    </footer>

    <!-- Dark Mode Script -->
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const body = document.getElementById('theme');

        themeToggle.addEventListener('click', () => {
            if (body.classList.contains('bg-gray-900')) {
                body.classList.replace('bg-gray-900', 'bg-gray-100');
                body.classList.replace('text-white', 'text-gray-900');
                themeToggle.innerHTML = '☀️ Light';
            } else {
                body.classList.replace('bg-gray-100', 'bg-gray-900');
                body.classList.replace('text-gray-900', 'text-white');
                themeToggle.innerHTML = '🌙 Dark';
            }
        });
    </script>

</body>
</html>
