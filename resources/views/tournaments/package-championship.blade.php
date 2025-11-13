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
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                            {{ $championship->competitors->count() }} Competitors
                        </span>
                    </div>
                </div>
                <a href="{{ route('tournaments.show', $tournament->id) }}"
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Tournament
                </a>
            </div>
        </div>

        <!-- Package Views with Tailwind Styling -->
        <div class="space-y-8">

            <!-- Fight List - Styled Version -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Fight Schedule</h3>
                @if($championship->fights->count() > 0)
                    @php
                        // Group fights by area directly from championship
                        $fightsByArea = $championship->fights->groupBy('area');
                    @endphp

                    @foreach($fightsByArea as $area => $fights)
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Area {{ $area }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">ID</th>
                                        <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">Competitor 1</th>
                                        <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">Competitor 2</th>
                                        <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">Winner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fights as $fight)
                                    @php
                                        $competitor1 = $fight->competitor1;
                                        $competitor2 = $fight->competitor2;
                                        $winner = null;
                                        if ($fight->winner_id) {
                                            $winner = $fight->winner_id == $fight->c1 ? $competitor1 : $competitor2;
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border-b text-sm text-gray-600">{{ $fight->short_id }}</td>
                                        <td class="px-4 py-2 border-b text-sm {{ $fight->winner_id == $fight->c1 ? 'font-bold text-green-600' : '' }}">
                                            {{ $competitor1 ? $competitor1->user->name : 'BYE' }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-sm {{ $fight->winner_id == $fight->c2 ? 'font-bold text-green-600' : '' }}">
                                            {{ $competitor2 ? $competitor2->user->name : 'BYE' }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-sm">
                                            @if($winner)
                                                <span class="text-green-600 font-medium">{{ $winner->user->name }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-gray-500">No fights scheduled yet.</p>
                @endif
            </div>

            <!-- Bracket Visualization -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Tournament Bracket</h3>

                @if($championship->fightersGroups->count() > 0)
                    <!-- Simple Bracket Display -->
                    <div class="space-y-6">
                        @php
                            $rounds = $championship->fightersGroups->groupBy('round');
                        @endphp

                        @foreach($rounds as $roundNumber => $groups)
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Round {{ $roundNumber }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(4, $groups->count()) }} gap-4">
                                @foreach($groups as $group)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="text-sm font-medium text-gray-700 mb-2 flex justify-between">
                                        <span>Group {{ $group->short_id }}</span>
                                        <span class="text-gray-500">Area {{ $group->area }}</span>
                                    </div>

                                    @if($group->fights->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($group->fights as $fight)
                                            @php
                                                $competitor1 = $fight->competitor1;
                                                $competitor2 = $fight->competitor2;
                                                $winner = null;
                                                if ($fight->winner_id) {
                                                    $winner = $fight->winner_id == $fight->c1 ? $competitor1 : $competitor2;
                                                }
                                            @endphp
                                            <div class="bg-white border border-gray-200 rounded p-3 text-sm">
                                                <div class="font-medium text-gray-800 mb-1 text-center">Fight {{ $fight->short_id }}</div>
                                                <div class="grid grid-cols-3 gap-2 items-center text-xs">
                                                    <span class="text-right truncate {{ $fight->winner_id == $fight->c1 ? 'font-bold text-green-600' : '' }}">
                                                        {{ $competitor1 ? $competitor1->user->name : 'BYE' }}
                                                    </span>
                                                    <span class="text-center text-gray-500">vs</span>
                                                    <span class="text-left truncate {{ $fight->winner_id == $fight->c2 ? 'font-bold text-green-600' : '' }}">
                                                        {{ $competitor2 ? $competitor2->user->name : 'BYE' }}
                                                    </span>
                                                </div>
                                                @if($winner)
                                                    <div class="text-green-600 text-xs font-medium mt-1 text-center">
                                                        Winner: {{ $winner->user->name }}
                                                    </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-sm text-center">No fights</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No bracket generated yet.</p>
                @endif
            </div>

            <!-- Competitors List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Competitors ({{ $championship->competitors->count() }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($championship->competitors as $competitor)
                    <div class="border border-gray-200 rounded-lg p-3 text-center bg-white hover:bg-gray-50 transition-colors">
                        <div class="font-medium text-gray-800 text-sm">{{ $competitor->user->name }}</div>
                        <div class="text-xs text-gray-600 mt-1">ID: {{ $competitor->short_id }}</div>
                        @if($competitor->confirmed)
                            <div class="text-xs text-green-600 font-medium mt-1">✓ Confirmed</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
