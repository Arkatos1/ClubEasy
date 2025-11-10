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
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Banner -->
    <div class="w-full h-48 bg-gradient-to-r from-blue-600 to-green-500 flex items-center justify-center">
        <h1 class="text-4xl font-bold text-white">Sports Club</h1>
    </div>

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Main Content - This will grow to push footer down -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('layouts.footer')
</body>
</html>
