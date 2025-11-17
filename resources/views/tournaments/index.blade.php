@extends('layouts.master')

@section('title', __('Tournaments'))

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
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ __('Tournament Generator') }}</h1>

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
            <div class="bootstrap-form mb-8 p-6 border border-gray-200 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">{{ __('Generate New Tournament') }}</h2>
                <form action="{{ route('tournaments.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
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

            <!-- Tournament Display -->
            @if($tournament && $tournament->championships->count() > 0)
                @foreach($tournament->championships as $championship)
                    <div class="mb-8 p-6 border border-gray-200 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">{{ $championship->name ?? __('Championship') }}</h2>

                        <!-- Championship Info -->
                        <div class="mb-4">
                            <h3 class="text-lg font-medium mb-2">{{ __('Championship Details') }}</h3>
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
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-2">
                                {{ $championship->category->isTeam ? __('Teams') : __('Competitors') }}
                            </h3>
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
                                            {{ $competitor->user->name ?? __('Unknown Competitor') }}
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
                                    <h3 class="text-lg font-medium mb-4">{{ __('Single Elimination Tree') }}</h3>
                                    @include('laravel-tournaments::partials.tree.singleElimination', [
                                        'championship' => $championship,
                                        'hasPreliminary' => $championship->settings && $championship->settings->hasPreliminary
                                    ])
                                </div>

                                <!-- Fights List -->
                                <div>
                                    <h3 class="text-lg font-medium mb-4">{{ __('Fights Schedule') }}</h3>
                                    @include('laravel-tournaments::partials.fights', [
                                        'championship' => $championship
                                    ])
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

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
@endsection
