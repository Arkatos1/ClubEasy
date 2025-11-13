<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $venue = Venue::first();

        // Get an existing user or create one
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Check if tournament already exists
        $tournament = Tournament::where('name', 'Demo Tournament')->first();

        if (!$tournament) {
            Tournament::create([
                'name' => 'Demo Tournament',
                'slug' => Str::slug('Demo Tournament'),
                'dateIni' => now(),
                'dateFin' => now()->addDays(3),
                'level_id' => 1,
                'type' => 1,
                'venue_id' => $venue->id,
                'user_id' => $user->id,
            ]);
        }

        // Get categories
        $individualCategory = \Xoco70\LaravelTournaments\Models\Category::where('name', 'Individual')->first();
        $teamCategory = \Xoco70\LaravelTournaments\Models\Category::where('name', 'Team')->first();

        // Create championships if they don't exist
        if ($tournament && $individualCategory) {
            Championship::firstOrCreate([
                'tournament_id' => $tournament->id,
                'category_id' => $individualCategory->id,
            ]);
        }

        if ($tournament && $teamCategory) {
            Championship::firstOrCreate([
                'tournament_id' => $tournament->id,
                'category_id' => $teamCategory->id,
            ]);
        }
    }
}
