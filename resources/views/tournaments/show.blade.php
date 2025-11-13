@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Tournament Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $tournament->name }}</h1>
                    <div class="flex items-center space-x-4 text-gray-600">
                        @if($tournament->dateIni)
                            <span>📅 {{ \Carbon\Carbon::parse($tournament->dateIni)->format('F j, Y') }}</span>
                        @else
                            <span>📅 Date TBD</span>
                        @endif
                        @if($tournament->venue)
                            <span>📍 {{ $tournament->venue->venue_name }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('tournaments.index') }}"
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Tournaments
                </a>
            </div>
        </div>

        <!-- Championships -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Championships</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($tournament->championships as $championship)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        Championship #{{ $championship->id }}
                    </h3>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Competitors:</span>
                            <span class="font-medium">{{ $championship->competitors->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fight Groups:</span>
                            <span class="font-medium">{{ $championship->fightersGroups->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Fights:</span>
                            <span class="font-medium">{{ $championship->fights->count() }}</span>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('tournaments.championship', ['tournament' => $tournament->id, 'championship' => $championship->id]) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                            View Bracket
                        </a>
                        <button class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                            Fight List
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tournament Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Tournament Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Details</h4>
                    <ul class="text-gray-600 space-y-1">
                        <li><strong>Sport:</strong> {{ $tournament->sport ?? 'Not specified' }}</li>
                        <li><strong>Type:</strong> {{ $tournament->type ?? 'Not specified' }}</li>
                        <li><strong>Created:</strong> {{ $tournament->created_at->diffForHumans() }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Location</h4>
                    @if($tournament->venue)
                        <p class="text-gray-600">{{ $tournament->venue->venue_name }}</p>
                        <p class="text-gray-600">{{ $tournament->venue->city }}</p>
                    @else
                        <p class="text-gray-600">Location not specified</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
