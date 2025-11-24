<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Xoco70\LaravelTournaments\Models\Competitor;

class TournamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // TEMPORARILY COMMENT OUT THIS EVENT LISTENER - it's causing conflicts
        /*
        Competitor::creating(function ($competitor) {
            if (empty($competitor->championship_id)) {
                \Log::error('Competitor being created without championship_id', [
                    'competitor_attributes' => $competitor->getAttributes()
                ]);

                // Try to get championship_id from context
                // If we can't get it, prevent creation
                if (empty($competitor->championship_id)) {
                    throw new \Exception('championship_id is required for competitor');
                }
            }
        });
        */
    }
}
