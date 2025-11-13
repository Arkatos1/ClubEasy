@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $tournament->name }}</h1>
                    <h2 class="text-xl text-gray-600 mb-4">Package Features Demo</h2>
                    <div class="flex items-center space-x-4 text-gray-600">
                        @if($championship->settings)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                @if($championship->hasPreliminary()) Preliminary Rounds @else Single Elimination @endif
                            </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('tournaments.show', $tournament->id) }}"
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Tournament
                </a>
            </div>
        </div>

        <!-- Package Views Section -->
        <div class="space-y-8">
            @if($championship->hasPreliminary())
                <!-- Preliminary Rounds -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Preliminary Rounds (Package View)</h3>
                    @include('laravel-tournaments::partials.tree.preliminary')
                </div>
            @endif

            <!-- Single Elimination Tree -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Single Elimination Bracket (Package View)</h3>
                @if($championship->fightersGroups->count() > 0)
                    @include('laravel-tournaments::partials.tree.singleElimination', ['hasPreliminary' => $championship->hasPreliminary()])
                @else
                    <p class="text-gray-500">No bracket generated yet.</p>
                @endif
            </div>

            <!-- Fight List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Fight List (Package View)</h3>
                @if($championship->fights->count() > 0)
                    @include('laravel-tournaments::partials.fights')
                @else
                    <p class="text-gray-500">No fights scheduled yet.</p>
                @endif
            </div>
        </div>

        <!-- Settings Info -->
        @if($championship->settings)
        <div class="bg-gray-50 rounded-lg p-6 mt-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Championship Settings</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><strong>Preliminary:</strong> {{ $championship->hasPreliminary() ? 'Yes' : 'No' }}</div>
                @if($championship->hasPreliminary())
                    <div><strong>Group Size:</strong> {{ $championship->settings->preliminaryGroupSize }}</div>
                    <div><strong>Winners:</strong> {{ $championship->settings->preliminaryWinner }}</div>
                @endif
                <div><strong>Fighting Areas:</strong> {{ $championship->settings->fightingAreas }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
