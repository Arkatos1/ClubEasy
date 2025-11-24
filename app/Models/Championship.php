<?php

namespace App\Models;

use Xoco70\LaravelTournaments\Models\Championship as BaseChampionship;

class Championship extends BaseChampionship
{
    /**
     * Override the relationship to use our custom Competitor model
     */
    public function competitors()
    {
        return $this->hasMany(\App\Models\Competitor::class);
    }

    /**
     * Create a competitor with proper championship_id
     */
    public function createCompetitor(array $attributes = [])
    {
        $attributes['championship_id'] = $this->id;
        return \App\Models\Competitor::create($attributes);
    }
}
