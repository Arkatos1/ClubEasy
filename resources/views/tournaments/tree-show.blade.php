@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $championship->tournament->name }}</h1>
                    <h2 class="text-xl text-gray-600 mb-4">Package-Generated Tournament Tree</h2>
                    <div class="flex items-center space-x-4 text-gray-600">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                            @if($championship->hasPreliminary()) Preliminary + Elimination @else Single Elimination @endif
                        </span>
                        <span>{{ $championship->competitors->count() }} competitors</span>
                        <span>{{ $championship->fightersGroups->count() }} fight groups</span>
                    </div>
                </div>
                <div class="space-x-2">
                    <a href="{{ route('tree.index') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        ← Back to Generator
                    </a>
                    <form action="{{ route('tree.store', $championship->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                            Regenerate Tree
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Package Tree Views -->
        <div class="space-y-8">

            @if($championship->hasPreliminary())
            <!-- Preliminary Rounds -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Preliminary Rounds</h3>
                @include('laravel-tournaments::partials.tree.preliminary')
            </div>
            @endif

            <!-- Single Elimination Tree -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Single Elimination Bracket</h3>
                @include('laravel-tournaments::partials.tree.singleElimination', ['hasPreliminary' => $championship->hasPreliminary() ? 1 : 0])
            </div>

            <!-- Fight List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Fight Schedule</h3>
                @include('laravel-tournaments::partials.fights')
            </div>

            <!-- Tree Update Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Update Tree Results</h3>
                <form method="POST" action="{{ route('tree.update', $championship) }}">
                    @csrf
                    @method('PUT')
                    <div class="text-center">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700">
                            Update Tournament Results
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm text-center mt-2">
                        This will process the results and advance winners through the bracket
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Basic styling for package brackets -->
<style>
.brackets-wrapper {
    position: relative;
    min-height: 400px;
}
.fight-box {
    border: 1px solid #e5e7eb;
    padding: 8px;
    margin: 4px;
    background: white;
    border-radius: 4px;
    min-width: 150px;
}
.vertical-connector {
    position: absolute;
    border-left: 2px solid #6b7280;
}
.horizontal-connector {
    position: absolute;
    border-top: 2px solid #6b7280;
    width: 20px;
}
</style>
@endsection
