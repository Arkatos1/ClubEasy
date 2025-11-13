@extends('layouts.master')

@section('title', 'Tournaments')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Tournament Generator</h1>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tournament Creation Form -->
                <div class="mb-8 p-6 border border-gray-200 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Generate New Tournament</h2>
                    <form action="{{ route('tournaments.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label for="numFighters" class="block text-sm font-medium text-gray-700">Number of Fighters/Teams</label>
                                <input type="number" name="numFighters" id="numFighters"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       min="2" max="64" value="8" required>
                            </div>

                            <div>
                                <label for="tree_type" class="block text-sm font-medium text-gray-700">Tournament Type</label>
                                <select name="tree_type" id="tree_type"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">Single Elimination</option>
                                    <option value="2">Single Elimination with Preliminary</option>
                                    <option value="3">Playoff</option>
                                </select>
                            </div>

                            <div>
                                <label for="isTeam" class="flex items-center">
                                    <input type="checkbox" name="isTeam" id="isTeam" value="1"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Team Competition</span>
                                </label>
                            </div>

                            <div id="preliminarySettings" style="display: none;">
                                <label for="preliminary_group_size" class="block text-sm font-medium text-gray-700">Preliminary Group Size</label>
                                <input type="number" name="preliminary_group_size" id="preliminary_group_size"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       min="2" max="8" value="3">
                            </div>
                        </div>

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Generate Tournament
                        </button>
                    </form>
                </div>

                <!-- Display Existing Tournament -->
                @if($tournament && $tournament->championships->count() > 0)
                    @foreach($tournament->championships as $championship)
                        <div class="mb-8 p-6 border border-gray-200 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">{{ $championship->name ?? 'Championship' }}</h2>

                            <!-- Championship Info -->
                            <div class="mb-4">
                                <h3 class="text-lg font-medium mb-2">Championship Details</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium">Type:</span>
                                        {{ $championship->category->isTeam ? 'Team' : 'Individual' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Fighters:</span>
                                        {{ $championship->category->isTeam ? $championship->teams->count() : $championship->competitors->count() }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Tree Type:</span>
                                        @if($championship->settings && $championship->settings->treeType)
                                            {{ $championship->settings->treeType == 1 ? 'Single Elimination' :
                                            ($championship->settings->treeType == 2 ? 'Single Elimination with Preliminary' : 'Playoff') }}
                                        @else
                                            Not Set
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-medium">Preliminary:</span>
                                        {{ $championship->settings && $championship->settings->hasPreliminary ? 'Yes' : 'No' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Fighters List -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-2">
                                    {{ $championship->category->isTeam ? 'Teams' : 'Competitors' }}
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    @if($championship->category->isTeam)
                                        @foreach($championship->teams as $team)
                                            <div class="bg-gray-100 p-2 rounded text-sm">
                                                {{ $team->name ?? 'Unknown Team' }}
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($championship->competitors as $competitor)
                                            <div class="bg-gray-100 p-2 rounded text-sm">
                                                {{ $competitor->user->name ?? 'Unknown Competitor' }}
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Tournament Trees -->
                            @if($championship->fightersGroups && $championship->fightersGroups->count() > 0)
                                <div class="space-y-8">
                                    @if($championship->settings && $championship->settings->hasPreliminary)
                                        <!-- Preliminary Round -->
                                        <div>
                                            <h3 class="text-lg font-medium mb-4">Preliminary Round</h3>
                                            @include('laravel-tournaments::partials.tree.preliminary', [
                                                'championship' => $championship,
                                                'hasPreliminary' => true
                                            ])
                                        </div>
                                    @endif

                                    <!-- Single Elimination Tree -->
                                    <div>
                                        <h3 class="text-lg font-medium mb-4">Single Elimination Tree</h3>
                                        @include('laravel-tournaments::partials.tree.singleElimination', [
                                            'championship' => $championship,
                                            'hasPreliminary' => $championship->settings && $championship->settings->hasPreliminary
                                        ])
                                    </div>

                                    <!-- Fights List -->
                                    <div>
                                        <h3 class="text-lg font-medium mb-4">Fights Schedule</h3>
                                        @include('laravel-tournaments::partials.fights', [
                                            'championship' => $championship
                                        ])
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                    No tournament trees generated yet. Use the form above to generate a tournament.
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                        No tournaments available. Generate your first tournament using the form above.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Show/hide preliminary settings based on tournament type
    document.getElementById('tree_type').addEventListener('change', function() {
        const preliminarySettings = document.getElementById('preliminarySettings');
        if (this.value == '2') { // Single Elimination with Preliminary
            preliminarySettings.style.display = 'block';
        } else {
            preliminarySettings.style.display = 'none';
        }
    });

    // Trigger change event on page load
    document.getElementById('tree_type').dispatchEvent(new Event('change'));
</script>
@endsection
