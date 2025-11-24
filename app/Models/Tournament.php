<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Xoco70\LaravelTournaments\Models\Tournament as BaseTournament;

class Tournament extends BaseTournament
{
    protected $casts = [
        'dateIni' => 'datetime',
        'dateFin' => 'datetime',
        'registerDateLimit' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($tournament) {
            // Find and delete the associated event
            \App\Models\Event::where('title', $tournament->name)
                             ->where('start_date', $tournament->dateIni)
                             ->where('end_date', $tournament->dateFin)
                             ->delete();
        });
    }
}
