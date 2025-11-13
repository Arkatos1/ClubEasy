@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Tournaments</h2>
            <a href="{{ route('tournaments.index') }}"
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                Refresh
            </a>
        </div>

        <!-- Tournaments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($tournaments as $tournament)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-green-400 p-4">
                    <h3 class="text-xl font-bold text-white">{{ $tournament->name }}</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($tournament->dateIni)->format('M j, Y') }}
                        </span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                            {{ $tournament->championships->count() }} Championships
                        </span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">
                        @if($tournament->venue)
                            📍 {{ $tournament->venue->venue_name ?? 'Venue TBD' }}
                        @else
                            📍 Location TBD
                        @endif
                    </p>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('tournaments.show', $tournament->id) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            View Details →
                        </a>
                        <span class="text-xs text-gray-500">
                            {{ $tournament->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">🏆</div>
                <h3 class="text-2xl font-bold text-gray-700 mb-4">No Tournaments Yet</h3>
                <p class="text-gray-600 mb-6">Run the seeder to create demo tournaments!</p>
            </div>
            @endforelse
        </div>

        <!-- Package Features -->
        <div class="bg-gray-50 rounded-lg p-8 mt-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Tournament System Features</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-4xl mb-4">⚔️</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Single Elimination</h4>
                    <p class="text-gray-600 text-sm">Classic knockout tournament format</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-4">🔢</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Bracket Generation</h4>
                    <p class="text-gray-600 text-sm">Automatic bracket and fight generation</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-4">👥</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Player Management</h4>
                    <p class="text-gray-600 text-sm">Manage competitors and teams</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-4">📊</div>
                    <h4 class="font-semibold text-gray-800 mb-2">Results Tracking</h4>
                    <p class="text-gray-600 text-sm">Track fights and winners</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
