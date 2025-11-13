@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Simple Tournament Generator</h1>
            <p class="text-gray-600 mb-6">This creates 8 competitors and generates a tournament bracket</p>

            <form action="{{ route('simple.tournament.generate') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-700">
                    Generate Tournament
                </button>
            </form>
        </div>

        @if($tournaments->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Existing Tournaments</h2>
            @foreach($tournaments as $tournament)
            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ $tournament->name }}</h3>
                <p class="text-gray-600">{{ $tournament->competitors->count() }} competitors</p>
                <a href="{{ route('simple.tournament.show', $tournament->id) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 mt-2 inline-block">
                    View Tournament
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
