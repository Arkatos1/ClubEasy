<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Tabs -->
            <div class="flex space-x-8">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Home</a>
                <a href="{{ url('/matches') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('matches') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Matches</a>
                <a href="{{ url('/results') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('results') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Results</a>
                <a href="{{ url('/tournaments') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('tournaments') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Tournaments</a>
                <a href="{{ url('/calendar') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('players') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Calendar</a>
                <a href="{{ url('/about') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('about') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">About Us</a>

                @auth
                    <!-- Membership Tab for all authenticated users -->
                    <a href="{{ route('membership.index') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('membership*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Membership</a>

                    <!-- Trainer-specific tabs -->
                    @if(auth()->user()->hasRole('trainer') || auth()->user()->hasRole('administrator'))
                        <a href="{{ route('trainer.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('trainer*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Trainer Panel</a>
                    @endif

                    <!-- Admin-specific tabs -->
                    @if(auth()->user()->hasRole('administrator'))
                        <a href="{{ url('/canvas') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('canvas*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Canvas</a>
                        <a href="/admin" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('admin*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Admin</a>
                        <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('users*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Users</a>
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
