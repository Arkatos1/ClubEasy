<?php
$prefix = "singleElimination";
if ($championship->hasPreliminary() && $roundNumber == 1) {
    $prefix = "preliminary";
}
$className = $prefix . "_select";
$selectName = $prefix . "_fighters[]";

?>
<!-- r = round, m = match, f = fighter -->
@if (isset($show_tree))
    {{ $fighter->name ?? 'Unknown Fighter' }}
@else
    <select name="{{ $selectName }}" class="{{$className}}" {{ $isSuccess ? "id=success" : '' }}>
        <option {{ $selected == '' ? ' selected' : '' }} ></option>
        @foreach ($championship->fighters as $fighter)
            @if ($fighter != null)
                <option {{ $selected != null && $selected->id == $fighter->id ? ' selected' : '' }} value="{{ $fighter->id ?? null }}">
                    {{ $fighter->name ?? 'Unknown Fighter' }}
                </option>
            @endif
        @endforeach
    </select>
@endif
