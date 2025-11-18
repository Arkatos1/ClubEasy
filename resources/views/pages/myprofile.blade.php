@extends('layouts.master')

@section('title', __('Profile'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">{{ __('Profile') }}</h2>

                <!-- Role-based profile content -->
                @if(auth()->user()->hasRole('administrator'))
                    <div class="bg-red-50 p-4 rounded-lg mb-4">
                        <h3 class="text-lg font-semibold text-red-800">{{ __('Administrator Panel') }}</h3>
                        <p class="text-red-600">{{ __('You have administrative privileges.') }}</p>
                        <a href="{{ route('users.index') }}" class="text-red-700 hover:text-red-900 font-medium">{{ __('Manage Users') }}</a>
                    </div>
                @endif

                @if(auth()->user()->hasRole('trainer'))
                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                        <h3 class="text-lg font-semibold text-blue-800">{{ __('Trainer Panel') }}</h3>
                        <p class="text-blue-600">{{ __('Access your training tools and schedules.') }}</p>
                        <a href="{{ route('trainer.dashboard') }}" class="text-blue-700 hover:text-blue-900 font-medium">{{ __('Go to Trainer Dashboard') }} →</a>
                    </div>
                @endif

                <!-- Regular user content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 border rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Your Membership') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('Manage your club membership and benefits.') }}</p>
                        <a href="{{ route('membership.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ __('View Membership') }}</a>
                    </div>

                    <div class="bg-white p-6 border rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Player Profile') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('Update your player information and preferences.') }}</p>
                        <a href="{{ route('players.index') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">{{ __('Manage Profile') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
