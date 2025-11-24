@extends('layouts.master')

@section('title', __('Tournament Management'))

@section('template_linked_css')
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

.bootstrap-form .form-control,
.bootstrap-form .form-select {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.bootstrap-form .btn {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border: 1px solid transparent;
    border-radius: 0.375rem;
}

.bootstrap-form .btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.tournament-bracket .btn-success {
    background-color: #10b981;
    border-color: #10b981;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    float: right;
    clear: both;
    margin-top: 20px;
}

.tournament-bracket .table-bordered {
    border: 1px solid #dee2e6;
    border-collapse: collapse;
}

.tournament-bracket .table-bordered th,
.tournament-bracket .table-bordered td {
    border: 1px solid #dee2e6;
    padding: 8px 12px;
}

.tournament-bracket .table-bordered th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.tournament-bracket .clearfix {
    clear: both;
}

/* New styles for tournament management */
.tournament-management {
    margin-bottom: 2rem;
}

.tournament-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    background: white;
}

.tournament-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.tournament-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-danger {
    background-color: #ef4444;
    border-color: #ef4444;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    border: none;
}

.btn-danger:hover {
    background-color: #dc2626;
}

.btn-warning {
    background-color: #f59e0b;
    border-color: #f59e0b;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    border: none;
}

.btn-warning:hover {
    background-color: #d97706;
}

.championship-card {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f9fafb;
}
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Access Control Check -->
            @if(!$canManage)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ __('Access Denied') }}</span>
                    </div>
                    <p class="mt-2">{{ __('Only trainers and administrators can manage tournaments.') }}</p>
                    <a href="{{ route('tournaments.list') }}" class="inline-block mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                        {{ __('View Public Tournaments') }}
                    </a>
                </div>
            @else
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('Tournament Management') }}</h1>
                    <a href="{{ route('tournaments.list') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('View Public Tournaments') }}
                    </a>
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
                <div class="bootstrap-form mb-8 p-6 border border-gray-200 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">{{ __('Generate Tournament') }}</h2>
                    <form action="{{ route('tournaments.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label for="tournament_id" class="block text-sm font-medium text-gray-700">{{ __('Select Existing Tournament') }}</label>
                                <select name="tournament_id" id="tournament_id"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">{{ __('Create New Tournament') }}</option>
                                    @foreach($tournaments as $tournament)
                                        <option value="{{ $tournament->id }}">{{ $tournament->name }} ({{ $tournament->created_at->format('M j, Y') }})</option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Leave empty to create a new tournament') }}</p>
                            </div>

                            <div>
                                <label for="numFighters" class="block text-sm font-medium text-gray-700">{{ __('Number of Fighters/Teams') }}</label>
                                <input type="number" name="numFighters" id="numFighters"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       min="2" max="64" value="8" required>
                            </div>

                            <div>
                                <label for="tree_type" class="block text-sm font-medium text-gray-700">{{ __('Tournament Type') }}</label>
                                <select name="tree_type" id="tree_type"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">{{ __('Single Elimination') }}</option>
                                    <option value="2">{{ __('Single Elimination with Preliminary') }}</option>
                                    <option value="3">{{ __('Playoff') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="isTeam" class="flex items-center">
                                    <input type="checkbox" name="isTeam" id="isTeam" value="1"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Team Competition') }}</span>
                                </label>
                            </div>

                            <div id="preliminarySettings" style="display: none;">
                                <label for="preliminary_group_size" class="block text-sm font-medium text-gray-700">{{ __('Preliminary Group Size') }}</label>
                                <input type="number" name="preliminary_group_size" id="preliminary_group_size"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       min="2" max="8" value="3">
                            </div>
                        </div>

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Generate Tournament') }}
                        </button>
                    </form>
                </div>

                <!-- Existing Tournaments -->
                <div class="tournament-management">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Existing Tournaments') }}</h2>

                    @if($tournaments->count() > 0)
                        @foreach($tournaments as $tournament)
                            <div class="tournament-card">
                                <div class="tournament-header">
                                    <div>
                                        <h3 class="text-xl font-semibold">{{ $tournament->name }}</h3>
                                        <p class="text-gray-600 text-sm mt-1">
                                            @php
                                                // Convert string dates to Carbon instances for formatting
                                                $dateIni = \Carbon\Carbon::parse($tournament->dateIni);
                                                $dateFin = \Carbon\Carbon::parse($tournament->dateFin);
                                            @endphp
                                            {{ $dateIni->format('d.m.Y') }} - {{ $dateFin->format('d.m.Y') }}
                                        </p>
                                    </div>
                                    <div class="tournament-actions">
                                        <a href="{{ route('tournaments.show', $tournament->championships->first()) }}"
                                           class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded text-sm">
                                            {{ __('View Public') }}
                                        </a>
                                        <form action="{{ route('tournaments.destroy', $tournament) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this tournament and all its championships?') }}')">
                                            @csrf
                                            <button type="submit" class="btn-danger text-sm">
                                                {{ __('Delete Tournament') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <p class="text-gray-600 mb-4">
                                    {{ __('Created') }}: {{ $tournament->created_at->format('M j, Y g:i A') }} |
                                    {{ __('Championships') }}: {{ $tournament->championships->count() }}
                                </p>

                                @if($tournament->championships->count() > 0)
                                    @foreach($tournament->championships as $championship)
                                        <div class="championship-card">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="text-lg font-medium">{{ $championship->name }}</h4>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('tournaments.show', $championship) }}"
                                                       class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded text-sm">
                                                        {{ __('View') }}
                                                    </a>
                                                    <form action="{{ route('championships.destroy', $championship) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this championship?') }}')">
                                                        @csrf
                                                        <button type="submit" class="btn-warning text-sm">
                                                            {{ __('Delete Championship') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Championship Info -->
                                            <div class="mb-4">
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                    <div>
                                                        <span class="font-medium">{{ __('Type') }}:</span>
                                                        {{ $championship->category->isTeam ? __('Team') : __('Individual') }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">{{ $championship->category->isTeam ? __('Teams') : __('Fighters') }}:</span>
                                                        {{ $championship->category->isTeam ? $championship->teams->count() : $championship->competitors->count() }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">{{ __('Tree Type') }}:</span>
                                                        @if($championship->settings && $championship->settings->treeType)
                                                            {{ $championship->settings->treeType == 1 ? __('Single Elimination') :
                                                            ($championship->settings->treeType == 2 ? __('Single Elimination with Preliminary') : __('Playoff')) }}
                                                        @else
                                                            {{ __('Not Set') }}
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">{{ __('Preliminary') }}:</span>
                                                        {{ $championship->settings && $championship->settings->hasPreliminary ? __('Yes') : __('No') }}
                                                    </div>
                                                </div>
                                            </div>

                                        <!-- Fighters List -->
                                        <div class="mb-4">
                                            <h5 class="text-md font-medium mb-2">
                                                {{ $championship->category->isTeam ? __('Teams') : __('Competitors') }}
                                                <span class="text-sm text-gray-500">
                                                    ({{ $championship->competitors->whereNotNull('user_id')->count() }} {{ __('registered') }} / {{ $championship->competitors->count() }} {{ __('total') }})
                                                </span>
                                            </h5>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                @if($championship->category->isTeam)
                                                    @foreach($championship->teams as $team)
                                                        <div class="bg-gray-100 p-2 rounded text-sm">
                                                            {{ $team->name ?? __('Unknown Team') }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @foreach($championship->competitors as $competitor)
                                                        <div class="bg-gray-100 p-2 rounded text-sm">
                                                            @if($competitor->user_id)
                                                                {{ $competitor->user->name ?? __('Unknown Competitor') }}
                                                            @else
                                                                <span class="text-gray-500 italic">{{ __('Available Spot') }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                            <!-- Tournament Trees -->
                                            @if($championship->fightersGroups && $championship->fightersGroups->count() > 0)
                                                <div class="space-y-8 tournament-bracket">
                                                    <!-- Single Elimination Tree -->
                                                    <div>
                                                        <h5 class="text-lg font-medium mb-4">{{ __('Single Elimination Tree') }}</h5>
                                                        @include('laravel-tournaments::partials.tree.singleElimination', [
                                                            'championship' => $championship,
                                                            'hasPreliminary' => $championship->settings && $championship->settings->hasPreliminary
                                                        ])
                                                    </div>

                                                    <!-- Fights List -->
                                                    <div>
                                                        <h5 class="text-lg font-medium mb-4">{{ __('Fights Schedule') }}</h5>
                                                        @include('laravel-tournaments::partials.fights', [
                                                            'championship' => $championship
                                                        ])
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                                    {{ __('No tournament tree generated yet for this championship.') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="bg-gray-100 p-4 rounded text-sm">
                                        {{ __('No championships in this tournament yet.') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="bg-gray-100 p-6 rounded-lg text-center">
                            <p class="text-gray-600">{{ __('No tournaments created yet. Generate your first tournament above!') }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@if($canManage)
<script>
document.getElementById('tree_type').addEventListener('change', function() {
    const preliminarySettings = document.getElementById('preliminarySettings');
    if (this.value == '2') {
        preliminarySettings.style.display = 'block';
    } else {
        preliminarySettings.style.display = 'none';
    }
});

document.getElementById('tree_type').dispatchEvent(new Event('change'));
</script>
@endif
@endsection
