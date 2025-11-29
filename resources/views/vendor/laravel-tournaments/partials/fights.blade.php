@php
    $roundTranslations = [
        'Quarter-Finals' => 'Čtvrt-finále',
        'Semi-Finals' => 'Semi-finále',
        'Final' => 'Finále',
        'Grand Final' => 'Velké finále',
        'Preliminary Round' => 'Předkolo'
    ];

    function translateAreaTitle($areaTitle, $translations) {
        foreach ($translations as $en => $cs) {
            if (str_contains($areaTitle, $en)) {
                return str_replace($en, $cs, $areaTitle);
            }
        }
        return $areaTitle;
    }
@endphp

@foreach($championship->fights()->get()->groupBy('area') as $fightsByArea)
    @php
        $areaTitle = __('Area') . ' ' . ($fightsByArea->get(0)->area ?? '');
        $translatedAreaTitle = translateAreaTitle($areaTitle, $roundTranslations);
    @endphp

    <h4>{{ $translatedAreaTitle }}</h4>
    <table class="table-bordered text-center" width="600">
        <th class="p-10 text-center" width="100">{{ __('Id') }}</th>
        <th class="p-10 text-center" width="250">{{ __('Competitor 1') }}</th>
        <th class="p-10 text-center" width="250">{{ __('Competitor 2') }}</th>

        <?php $fightId = 0; ?>
        @foreach($fightsByArea as $fight)
            @if ($fight->shouldBeInFightList(false))
                <?php
                $fighter1 = optional($fight->fighter1)->name ?? __("TBD");
                $fighter2 = optional($fight->fighter2)->name ?? __("TBD");
                $fightId++;
                ?>

                <tr>
                    <td class="p-10">{{$fightId}}</td>
                    <td class="p-10">{{ $fighter1 }}</td>
                    <td class="p-10">{{ $fighter2 }}</td>
                </tr>
            @endif
        @endforeach
    </table>
    <br/><br/>
@endforeach
