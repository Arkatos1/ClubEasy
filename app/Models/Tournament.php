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
}
