@extends('layouts.master')

@section('title', __('Tournament Details'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <a href="{{ route('tournaments.list') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                        &larr; {{ __('Back to Tournaments') }}
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $championship->tournament->name }}</h1>
                    <p class="text-gray-600 mt-2">
                        @php
                            // Convert string dates to Carbon instances for formatting
                            $dateIni = \Carbon\Carbon::parse($championship->tournament->dateIni);
                            $dateFin = \Carbon\Carbon::parse($championship->tournament->dateFin);
                        @endphp
                        {{ $dateIni->format('d.m.Y H:i') }} - {{ $dateFin->format('d.m.Y H:i') }}
                    </p>
                </div>

                @if($canManage)
                    <a href="{{ route('tournaments.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Manage Tournament') }}
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

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Tournament Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Tournament Information') }}</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Type') }}:</span>
                            <span class="font-medium">{{ $championship->category->isTeam ? __('Team') : __('Individual') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Tree Type') }}:</span>
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

                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Participants') }}:</span>
                            <span class="font-medium">
                                @php
                                    $realParticipants = $championship->competitors()
                                        ->whereDoesntHave('user', function($query) use ($championship) {
                                            $query->where('email', 'LIKE', "placeholder_{$championship->id}_%@example.com");
                                        })
                                        ->count();
                                @endphp
                                {{ $realParticipants }}
                                @if($championship->settings && $championship->settings->limitByEntity > 0)
                                    / {{ $championship->settings->limitByEntity }}
                                @endif
                            </span>
                        </div>

                        @if($championship->settings && $championship->settings->hasPreliminary)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Preliminary Groups') }}:</span>
                                <span class="font-medium">{{ $championship->settings->preliminaryGroupSize ?? 3 }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Participation Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Participation') }}</h3>

                    @auth
                        @if($isRegistered)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-green-800 font-medium">{{ __('You are registered for this tournament') }}</span>
                                </div>

                                <form action="{{ route('tournaments.leave', $championship) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('{{ __('Are you sure you want to leave this tournament?') }}')"
                                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded text-sm">
                                        {{ __('Leave Tournament') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            @if($hasActiveMembership)
                                @if($championship->settings && $championship->settings->limitByEntity > 0 && $championship->competitors->count() >= $championship->settings->limitByEntity)
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-yellow-800 font-medium">{{ __('Tournament is full') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('tournaments.join', $championship) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                            {{ __('Join Tournament') }}
                                        </button>
                                    </form>
                                    <p class="text-sm text-gray-600 mt-2 text-center">
                                        {{ __('Available spots') }}:
                                        @if($championship->settings && $championship->settings->limitByEntity > 0)
                                            @php
                                                $availableSpots = $championship->competitors()
                                                    ->whereHas('user', function($query) use ($championship) {
                                                        $query->where('email', 'LIKE', "placeholder_{$championship->id}_%@example.com");
                                                    })
                                                    ->count();
                                            @endphp
                                            {{ $availableSpots }}
                                        @else
                                            @php
                                                // For unlimited tournaments, show remaining placeholder spots
                                                $availableSpots = $championship->competitors()
                                                    ->whereHas('user', function($query) use ($championship) {
                                                        $query->where('email', 'LIKE', "placeholder_{$championship->id}_%@example.com");
                                                    })
                                                    ->count();
                                            @endphp
                                            {{ $availableSpots }}
                                        @endif
                                    </p>
                                @endif
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-blue-800 font-medium">{{ __('You must be a member to join tournaments') }}</span>
                                    </div>
                                    <a href="{{ route('membership.index') }}" class="inline-block mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                                        {{ __('Become a Member') }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-gray-600 mb-3">{{ __('Please log in to join this tournament') }}</p>
                            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                                {{ __('Login') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Participants List -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">{{ __('Participants') }}
                    <span class="text-sm text-gray-500 font-normal">
                        @php
                            $realParticipants = $championship->competitors()
                                ->whereDoesntHave('user', function($query) use ($championship) {
                                    $query->where('email', 'LIKE', "placeholder_{$championship->id}_%@example.com");
                                })
                                ->count();
                            $totalSpots = $championship->competitors()->count();
                        @endphp
                        ({{ $realParticipants }} {{ __('registered') }} / {{ $totalSpots }} {{ __('total') }})
                    </span>
                </h3>

                @if($championship->competitors->count() > 0)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                            @foreach($championship->competitors as $competitor)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm">{{ $loop->iteration }}</span>
                                    </div>
                                    <div>
                                        @if($competitor->user_id)
                                            @if(strpos($competitor->user->email ?? '', 'placeholder_') === 0 || $competitor->user->first_name == __('Available Spot'))
                                                <p class="text-gray-500 italic">{{ __('Available Spot') }}</p>
                                            @else
                                                <p class="font-medium text-gray-900">
                                                    {{ $competitor->user->name ?? __('Unknown Competitor') }}
                                                </p>
                                                @if($isRegistered && auth()->id() === $competitor->user_id)
                                                    <span class="text-xs text-green-600 font-medium">{{ __('You') }}</span>
                                                @endif
                                            @endif
                                        @else
                                            <p class="text-gray-500 italic">{{ __('Available Spot') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No participants yet') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Be the first to join this tournament!') }}</p>
                    </div>
                @endif
            </div>

            <!-- Tournament Tree Preview -->
            @if($championship->fightersGroups->count() > 0)
                <div class="space-y-8">
                    <!-- Tournament Tree Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4">{{ __('Tournament Tree') }}</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 tournament-bracket">
                            <style>
                            .tournament-bracket #brackets-wrapper,
                            .tournament-bracket #round-titles-wrapper {
                                position: relative;
                                margin-top: 20px;
                                float: left;
                            }

                            .tournament-bracket #brackets-wrapper {
                                top: 70px;
                                min-height: 400px;
                            }

                            .tournament-bracket .round-title {
                                height: 30px;
                                text-align: center;
                                line-height: 30px;
                                position: absolute;
                                width: 150px;
                                background-color: #f5f5f5;
                                border: 1px solid #cdc9c9;
                                box-sizing: border-box;
                                font-weight: bold;
                                font-size: 16px;
                                color: #374151;
                            }

                            .tournament-bracket .match-wrapper {
                                border: 1px solid #cdc9c9;
                                box-sizing: border-box;
                                position: absolute;
                                width: 150px;
                                background-color: #f5f5f5;
                                padding: 0;
                            }

                            .tournament-bracket .match-divider {
                                width: 100%;
                                float: left;
                                border-top: 1px solid #cdc9c9;
                                margin: 0;
                            }

                            .tournament-bracket .horizontal-connector,
                            .tournament-bracket .vertical-connector {
                                position: absolute;
                            }

                            .tournament-bracket .vertical-connector {
                                border-left: 3px solid #cdc9c9 !important;
                                width: 3px;
                            }

                            .tournament-bracket .horizontal-connector {
                                border-top: 3px solid #cdc9c9 !important;
                                width: 20px;
                            }

                            .tournament-bracket .player-wrapper {
                                background-color: #f5f5f5;
                                box-sizing: border-box;
                                padding-left: 5px;
                                width: 80%;
                            }

                            .tournament-bracket .score {
                                background-color: #f0f0f0;
                                box-sizing: border-box;
                                text-align: center;
                                width: 20%;
                                border: 0;
                                font-size: 16px;
                                font-family: arial, sans-serif;
                            }

                            .tournament-bracket .player-wrapper,
                            .tournament-bracket .score {
                                float: right !important;
                                height: 30px;
                                line-height: 30px;
                                overflow: hidden;
                            }

                            .tournament-bracket .singleElimination_select {
                                border: 0 none;
                                height: 30px;
                                width: 80%;
                                background-color: #f5f5f5;
                                -webkit-appearance: none;
                                -moz-appearance: none;
                                text-indent: 1px;
                                text-overflow: '';
                                font-size: 16px;
                                font-family: arial, sans-serif;
                                padding: 0 5px;
                            }

                            .tournament-bracket #success {
                                background-color: #DFF2BF !important;
                            }

                            .tournament-bracket .match-wrapper > div {
                                clear: both;
                                width: 100%;
                            }

                            .tournament-bracket .match-wrapper > div:first-child {
                                margin-bottom: 0;
                            }

                            .tournament-bracket .clearfix {
                                clear: both;
                            }
                            </style>

                            @include('laravel-tournaments::partials.tree.singleElimination', [
                                'championship' => $championship,
                                'hasPreliminary' => $championship->settings && $championship->settings->hasPreliminary
                            ])
                        </div>
                    </div>

                    <!-- Fights List -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4">{{ __('Fights Schedule') }}</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            @include('laravel-tournaments::partials.fights', [
                                'championship' => $championship
                            ])
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Tournament tree not generated yet') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('The tournament organizer will generate the tree when ready.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
