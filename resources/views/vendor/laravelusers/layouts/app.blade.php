<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if (trim($__env->yieldContent('template_title')))@yield('template_title') | @endif Sports Club App</title>

    {{-- Bootstrap Styles --}}
    @if(config('laravelusers.enableBootstrapCssCdn'))
        <link rel="stylesheet" type="text/css" href="{{ config('laravelusers.bootstrapCssCdn') }}">
    @endif

    {{-- Custom Styles to match your site --}}
    <style>
        .sports-banner {
            background: linear-gradient(to right, #2563eb, #10b981);
            height: 12rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sports-nav {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-tab {
            color: #374151;
            font-weight: 500;
            text-decoration: none;
            padding: 0.5rem 0;
            margin: 0 1rem;
            border-bottom: 2px solid transparent;
        }
        .nav-tab:hover {
            color: #2563eb;
        }
        .nav-tab.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }
        .sports-footer {
            background: #1f2937;
            color: white;
            margin-top: 3rem;
        }
    </style>

    @yield('template_linked_css')

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body style="background-color: #f9fafb; font-family: 'Instrument Sans', sans-serif;">
    <div id="app">
        {{-- Sports Club Banner --}}
        <div class="sports-banner">
            <h1 style="color: white; font-size: 2.25rem; font-weight: bold;">Sports Club</h1>
        </div>

        {{-- Sports Club Navigation --}}
        <nav class="sports-nav">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center py-3">
                    {{-- Navigation Tabs --}}
                    <div class="d-flex">
                        <a href="{{ url('/') }}" class="nav-tab {{ request()->is('/') ? 'active' : '' }}">Home</a>
                        <a href="{{ url('/players') }}" class="nav-tab {{ request()->is('players') ? 'active' : '' }}">Players</a>
                        <a href="{{ url('/matches') }}" class="nav-tab {{ request()->is('matches') ? 'active' : '' }}">Matches</a>
                        <a href="{{ url('/results') }}" class="nav-tab {{ request()->is('results') ? 'active' : '' }}">Results</a>
                        <a href="{{ url('/about') }}" class="nav-tab {{ request()->is('about') ? 'active' : '' }}">About Us</a>

                        @auth
                            <a href="{{ route('membership.index') }}" class="nav-tab {{ request()->is('membership*') ? 'active' : '' }}">Membership</a>

                            @if(auth()->user()->hasRole('trainer') || auth()->user()->hasRole('administrator'))
                                <a href="{{ route('trainer.dashboard') }}" class="nav-tab {{ request()->is('trainer*') ? 'active' : '' }}">Trainer Panel</a>
                            @endif

                            @if(auth()->user()->hasRole('administrator'))
                                <a href="/admin" class="nav-tab {{ request()->is('admin*') ? 'active' : '' }}">Admin</a>
                                <a href="{{ route('users.index') }}" class="nav-tab {{ request()->is('users*') ? 'active' : '' }}">Users</a>
                            @endif
                        @endauth
                    </div>

                    {{-- Auth Links --}}
                    <div class="d-flex align-items-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-tab me-3">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-tab p-0" style="text-decoration: none;">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="nav-tab me-3">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="container py-4">
            @yield('content')
        </main>

        {{-- Sports Club Footer --}}
        <footer class="sports-footer">
            <div class="container py-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h5>Sports Club</h5>
                        <p class="text-muted">Your premier destination for sports management and tournament organization.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="/" class="text-muted text-decoration-none">Home</a></li>
                            <li><a href="/players" class="text-muted text-decoration-none">Players</a></li>
                            <li><a href="/matches" class="text-muted text-decoration-none">Matches</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>Contact</h5>
                        <p class="text-muted mb-1">Email: info@sportsclub.com</p>
                        <p class="text-muted">Phone: (123) 456-7890</p>
                    </div>
                </div>
                <div class="border-top border-gray-600 pt-3 text-center text-muted">
                    <p>&copy; 2024 Sports Club. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    {{-- Bootstrap Scripts --}}
    @if(config('laravelusers.enablejQueryCdn'))
        <script src="{{ asset(config('laravelusers.jQueryCdn')) }}"></script>
    @endif
    @if(config('laravelusers.enableBootstrapPopperJsCdn'))
        <script src="{{ asset(config('laravelusers.bootstrapPopperJsCdn')) }}"></script>
    @endif
    @if(config('laravelusers.enableBootstrapJsCdn'))
        <script src="{{ asset(config('laravelusers.bootstrapJsCdn')) }}"></script>
    @endif

    @yield('template_scripts')
</body>
</html>
