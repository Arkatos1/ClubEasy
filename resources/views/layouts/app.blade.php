<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Club App</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Banner -->
    <div class="w-full h-48 bg-gradient-to-r from-blue-600 to-green-500 flex items-center justify-center">
        <h1 class="text-4xl font-bold text-white">Sports Club</h1>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Tabs -->
                <div class="flex space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Home</a>
                    <a href="{{ url('/players') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('players') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Players</a>
                    <a href="{{ url('/matches') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('matches') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Matches</a>
                    <a href="{{ url('/results') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('results') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Results</a>
                    <a href="{{ url('/about') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('about') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">About Us</a>

                    @auth
                        <!-- Membership Tab for all authenticated users -->
                        <a href="{{ route('membership.index') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('membership*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Membership</a>

                        <!-- Trainer-specific tabs -->
                        @if($is_trainer || $is_admin)
                            <a href="{{ route('trainer.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('trainer*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Trainer Panel</a>
                        @endif

                        <!-- Admin-specific tabs -->
                        @if($is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('admin*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Admin</a>
                        @endif
                    @endauth
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-blue-600">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Sports Club</h3>
                    <p class="text-gray-300">Your premier destination for sports management and tournament organization.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="/players" class="text-gray-300 hover:text-white">Players</a></li>
                        <li><a href="/matches" class="text-gray-300 hover:text-white">Matches</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-300">Email: info@sportsclub.com</p>
                    <p class="text-gray-300">Phone: (123) 456-7890</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-300">
                <p>&copy; 2024 Sports Club. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
