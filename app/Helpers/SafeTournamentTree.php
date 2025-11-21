<?php
// Disable errors for this view to prevent crashes
try {
    $singleEliminationTree = $championship->fightersGroups->where('round', '>=', $hasPreliminary + 1)->groupBy('round');
    $hasValidTreeData = $singleEliminationTree->count() > 0 && $championship->fightersGroups->count() > 0;

    if ($hasValidTreeData) {
        $treeGen = app()->make(\Xoco70\LaravelTournaments\TreeGen\CreateSingleEliminationTree::class, [
            'groupsByRound' => $singleEliminationTree,
            'championship' => $championship,
            'hasPreliminary' => $hasPreliminary
        ]);

        $treeGen->build();

        // Use reflection to safely access brackets
        $reflection = new ReflectionClass($treeGen);
        $bracketsProperty = $reflection->getProperty('brackets');
        $bracketsProperty->setAccessible(true);
        $brackets = $bracketsProperty->getValue($treeGen) ?? [];

        $noRoundsProperty = $reflection->getProperty('noRounds');
        $noRoundsProperty->setAccessible(true);
        $noRounds = $noRoundsProperty->getValue($treeGen) ?? 0;
    } else {
        $brackets = [];
        $noRounds = 0;
    }
} catch (Exception $e) {
    $hasValidTreeData = false;
    $brackets = [];
    $noRounds = 0;
    \Log::error('Tree display error: ' . $e->getMessage());
}
?>

@if ($hasValidTreeData && !empty($brackets))
    @if (Request::is('championships/'.$championship->id.'/pdf'))
        <h1>{{ $championship->buildName() }}</h1>
    @endif

    <form method="POST" action="{{ route('tournaments.update', ['championship' => $championship->id]) }}" accept-charset="UTF-8">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" id="activeTreeTab" name="activeTreeTab" value="{{ $championship->id }}"/>

        @if(method_exists($treeGen, 'printRoundTitles'))
            {!! $treeGen->printRoundTitles() !!}
        @endif

        <div id="brackets-wrapper" style="padding-bottom: {{ max(100, ($championship->groupsByRound(1)->count() / 2 * 205)) }}px">
            @foreach ($brackets as $roundNumber => $round)
                @if(is_iterable($round))
                    @foreach ($round as $matchNumber => $match)
                        @if(is_array($match) || is_object($match))
                            @include('laravel-tournaments::partials.tree.brackets.fight', [
                                'match' => is_array($match) ? ($match['match'] ?? $match) : $match
                            ])

                            @if ($roundNumber != $noRounds)
                                @if(isset($match['vConnectorTop']) && isset($match['vConnectorLeft']) && isset($match['vConnectorHeight']))
                                    <div class="vertical-connector"
                                         style="top: {{ $match['vConnectorTop'] }}px; left: {{ $match['vConnectorLeft'] }}px; height: {{ $match['vConnectorHeight'] }}px;"></div>
                                @endif

                                @if(isset($match['hConnectorTop']) && isset($match['hConnectorLeft']))
                                    <div class="horizontal-connector"
                                         style="top: {{ $match['hConnectorTop'] }}px; left: {{ $match['hConnectorLeft'] }}px;"></div>
                                @endif

                                @if(isset($match['hConnector2Top']) && isset($match['hConnector2Left']))
                                    <div class="horizontal-connector"
                                         style="top: {{ $match['hConnector2Top'] }}px; left: {{ $match['hConnector2Left'] }}px;"></div>
                                @endif
                            @endif
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="clearfix"></div>
        <div align="right">
            <button type="submit" class="btn btn-success" id="update">
                Update Tree
            </button>
        </div>
    </form>
@else
    <div class="alert alert-info">
        @if(!$hasValidTreeData)
            No single elimination tree generated yet. Please generate the tournament tree first.

            @if($championship->fightersGroups->count() == 0)
                <br><small>No fighter groups found for this championship.</small>
            @elseif(isset($singleEliminationTree) && $singleEliminationTree->count() == 0)
                <br><small>No single elimination rounds found (only preliminary rounds may exist).</small>
            @endif
        @else
            Tournament tree was generated but no brackets could be displayed. This might be due to insufficient participants or a configuration issue.
        @endif
    </div>
@endif
