@extends('layouts.master')

@section('title', __('Tournaments'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Tournament List') }}</h1>
                @if($canManage)
                    <a href="{{ route('tournaments.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Manage Tournaments') }}
                    </a>
                @endif
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @php
                // Separate tournaments into upcoming and past
                $upcomingTournaments = $tournaments->filter(function($tournament) {
                    return \Carbon\Carbon::parse($tournament->dateFin) >= now();
                });

                $pastTournaments = $tournaments->filter(function($tournament) {
                    return \Carbon\Carbon::parse($tournament->dateFin) < now();
                });
            @endphp

            @if($upcomingTournaments->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Upcoming Tournaments') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($upcomingTournaments as $tournament)
                            @if($tournament->championships->count() > 0)
                                @foreach($tournament->championships as $championship)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                        <div class="p-6">
                                            <div class="flex justify-between items-start mb-3">
                                                <h3 class="text-xl font-semibold text-gray-900">{{ $tournament->name }}</h3>
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $championship->category->isTeam ? __('Team') : __('Individual') }}
                                                </span>
                                            </div>

                                            <p class="text-gray-600 text-sm mb-4">
                                                @php
                                                    $dateIni = \Carbon\Carbon::parse($tournament->dateIni);
                                                    $dateFin = \Carbon\Carbon::parse($tournament->dateFin);
                                                @endphp
                                                {{ $dateIni->format('d.m.Y H:i') }} - {{ $dateFin->format('d.m.Y H:i') }}
                                            </p>

                                            <div class="space-y-2 mb-4">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">{{ __('Type') }}:</span>
                                                    <span class="font-medium">
                                                        @if($championship->settings && $championship->settings->treeType)
                                                            @if($championship->settings->treeType == 1)
                                                                {{ __('Single Elimination') }}
                                                            @elseif($championship->settings->treeType == 2)
                                                                {{ __('Single Elimination with Preliminary') }}
                                                            @else
                                                                {{ __('Playoff') }}
                                                            @endif
                                                        @else
                                                            {{ __('Not Set') }}
                                                        @endif
                                                    </span>
                                                </div>

                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">{{ __('Participants') }}:</span>
                                                    <span class="font-medium">
                                                        @php
                                                            $realParticipants = $championship->competitors()
                                                                ->whereDoesntHave('user', function($query) use ($championship) {
                                                                    $query->where('email', 'LIKE', "placeholder_{$championship->id}_%@example.com");
                                                                })
                                                                ->count();
                                                            $totalSpots = $championship->competitors()->count();
                                                        @endphp
                                                        {{ $realParticipants }} / {{ $totalSpots }}
                                                        @if($championship->settings && $championship->settings->limitByEntity > 0)
                                                            ({{ __('Max') }}: {{ $championship->settings->limitByEntity }})
                                                        @endif
                                                    </span>
                                                </div>

                                                @if($championship->settings && $championship->settings->hasPreliminary)
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-500">{{ __('Preliminary') }}:</span>
                                                        <span class="font-medium text-green-600">{{ __('Yes') }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex justify-between items-center">
                                                <a href="{{ route('tournaments.show', $championship) }}"
                                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                                    {{ __('View Details') }}
                                                </a>

                                                @if($championship->fightersGroups->count() > 0)
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                        {{ __('Tree Generated') }}
                                                    </span>
                                                @else
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                        {{ __('Not Started') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if($pastTournaments->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Past Tournaments') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pastTournaments as $tournament)
                            @if($tournament->championships->count() > 0)
                                @foreach($tournament->championships as $championship)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow opacity-75">
                                        <div class="p-6">
                                            <div class="flex justify-between items-start mb-3">
                                                <h3 class="text-xl font-semibold text-gray-900">{{ $tournament->name }}</h3>
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $championship->category->isTeam ? __('Team') : __('Individual') }}
                                                </span>
                                            </div>

                                            <p class="text-gray-600 text-sm mb-4">
                                                @php
                                                    $dateIni = \Carbon\Carbon::parse($tournament->dateIni);
                                                    $dateFin = \Carbon\Carbon::parse($tournament->dateFin);
                                                @endphp
                                                {{ $dateIni->format('d.m.Y H:i') }} - {{ $dateFin->format('d.m.Y H:i') }}
                                            </p>

                                            <div class="space-y-2 mb-4">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">{{ __('Type') }}:</span>
                                                    <span class="font-medium">
                                                        @if($championship->settings && $championship->settings->treeType)
                                                            @if($championship->settings->treeType == 1)
                                                                {{ __('Single Elimination') }}
                                                            @elseif($championship->settings->treeType == 2)
                                                                {{ __('Single Elimination with Preliminary') }}
                                                            @else
                                                                {{ __('Playoff') }}
                                                            @endif
                                                        @else
                                                            {{ __('Not Set') }}
                                                        @endif
                                                    </span>
                                                </div>

                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">{{ __('Participants') }}:</span>
                                                    <span class="font-medium">
                                                        @php
                                                            $realParticipants = $championship->competitors()
                                                                ->whereDoesntHave('user', function($query) use ($championship) {
                                                                    $query->where('email', 'LIKE', "placeholder_{$championship->id}_%@example.com");
                                                                })
                                                                ->count();
                                                            $totalSpots = $championship->competitors()->count();
                                                        @endphp
                                                        {{ $realParticipants }} / {{ $totalSpots }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex justify-between items-center">
                                                <a href="{{ route('tournaments.show', $championship) }}"
                                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                                    {{ __('View Details') }}
                                                </a>

                                                @if($championship->fightersGroups->count() > 0)
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                        {{ __('Completed') }}
                                                    </span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                        {{ __('Not Completed') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if($tournaments->count() == 0)
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No tournaments available') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Check back later for upcoming tournaments.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
