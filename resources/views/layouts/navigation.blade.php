<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Tabs -->
            <div class="flex space-x-8">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('Home') }}</a>
                <a href="{{ url('/sports') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('matches') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('Matches') }}</a>
                <a href="{{ route('tournaments.list') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('tournaments*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('Tournaments') }}</a>
                <a href="{{ url('/calendar') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('calendar') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('Calendar') }}</a>
                <a href="{{ url('/gallery') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('gallery') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('Photo Gallery') }}</a>
                <a href="{{ url('/about') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('about') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('About Us') }}</a>

                @auth
                    {{-- Membership Tab for users and above --}}
                    @if(auth()->user()->hasRole('registered') || auth()->user()->hasRole('member') || auth()->user()->hasRole('trainer') || auth()->user()->hasRole('administrator'))
                        <a href="{{ route('membership.index') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('membership*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">{{ __('Membership') }}</a>
                    @endif

                    <!-- Single Admin tab -->
                    @if(auth()->user()->hasRole('administrator'))
                        <a href="{{ url('/administration') }}" class="text-gray-700 hover:text-blue-600 font-medium {{ request()->is('administration*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Administrace</a>
                    @endif
                @endauth
            </div>

            <!-- Auth Links -->
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ url('myprofile') }}" class="text-gray-700 hover:text-blue-600 font-medium">{{ __('Account') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-blue-600">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ __('Register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
