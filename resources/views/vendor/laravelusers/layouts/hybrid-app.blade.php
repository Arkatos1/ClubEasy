<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Club App - User Management</title>

    <!-- Tailwind Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap for Package Content Only -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">

    @yield('template_linked_css')
</head>
<body class="bg-gray-50">
    <!-- Banner -->
    <div class="w-full h-48 bg-gradient-to-r from-blue-600 to-green-500 flex items-center justify-center">
        <h1 class="text-4xl font-bold text-white">Sports Club</h1>
    </div>

    <!-- Unified navigation -->
    @include('layouts.navigation')

    <!-- Main Content Area with Bootstrap -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                {{-- Bootstrap Content Here --}}
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Unified footer -->
    @include('layouts.footer')

    <!-- Bootstrap & jQuery for Package Functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('template_scripts')
</body>
</html>
