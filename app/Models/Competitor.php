<?php

namespace App\Models;

use Xoco70\LaravelTournaments\Models\Competitor as BaseCompetitor;

class Competitor extends BaseCompetitor
{
    public function getFillable()
    {
        return [
            'championship_id',
            'user_id',
            'short_id',
            'confirmed'
        ];
    }

    // Fix package mapping
    public function getTournamentCategoryIdAttribute()
    {
        return $this->championship_id;
    }

    public function setTournamentCategoryIdAttribute($value)
    {
        $this->attributes['championship_id'] = $value;
    }
}
