@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Package Tournament Generator</h1>
            <p class="text-gray-600">Generate tournaments using the package's actual features</p>
        </div>

        <!-- Tournament Generation Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Generate New Tournament</h2>

            <form action="{{ route('tree.store', 1) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Competitors</label>
                        <select name="numFighters" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="4">4 Competitors</option>
                            <option value="8">8 Competitors</option>
                            <option value="16">16 Competitors</option>
                            <option value="32">32 Competitors</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tournament Type</label>
                        <select name="isTeam" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="0">Individual Competition</option>
                            <option value="1">Team Competition</option>
                        </select>
                    </div>
                </div>

                <!-- Championship Settings -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Championship Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preliminary Rounds</label>
                            <select name="hasPreliminary" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="0">No Preliminary</option>
                                <option value="1">With Preliminary</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preliminary Group Size</label>
                            <select name="preliminaryGroupSize" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="3">3 fighters</option>
                                <option value="4">4 fighters</option>
                                <option value="5">5 fighters</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fighting Areas</label>
                            <select name="fightingAreas" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="1">1 Area</option>
                                <option value="2">2 Areas</option>
                                <option value="4">4 Areas</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                        Generate Tournament Tree
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Tournaments -->
        @if($tournament && $tournament->championships->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Existing Tournaments</h2>

            @foreach($tournament->championships as $championship)
            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $tournament->name }}</h3>
                        <p class="text-gray-600">
                            Championship #{{ $championship->id }} |
                            {{ $championship->competitors->count() }} competitors |
                            @if($championship->hasPreliminary()) Preliminary + Elimination @else Single Elimination @endif
                        </p>
                    </div>
                    <div class="space-x-2">
                        @if($championship->fightersGroups->count() > 0)
                            <a href="{{ route('tree.show', $championship->id) }}"
                               class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                View Tree
                            </a>
                        @endif
                        <form action="{{ route('tree.store', $championship->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                Regenerate
                            </button>
                        </form>
                    </div>
                </div>

                @if($championship->fightersGroups->count() > 0)
                <div class="mt-3">
                    <h4 class="font-semibold text-gray-700 mb-2">Generated Structure:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                        <div class="bg-gray-50 p-2 rounded">
                            <div class="font-medium">{{ $championship->fightersGroups->count() }}</div>
                            <div class="text-gray-600">Fight Groups</div>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <div class="font-medium">{{ $championship->fights->count() }}</div>
                            <div class="text-gray-600">Total Fights</div>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <div class="font-medium">{{ $championship->fightersGroups->groupBy('round')->count() }}</div>
                            <div class="text-gray-600">Rounds</div>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <div class="font-medium">{{ $championship->settings->fightingAreas ?? 1 }}</div>
                            <div class="text-gray-600">Fighting Areas</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
