<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\Category;
use Xoco70\LaravelTournaments\Models\Venue;

class TournamentDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo tournament data...');

        // Create a venue
        $venue = Venue::create([
            'name' => 'Sports Club Main Arena',
            'address' => '123 Sports Street',
            'city' => 'Sports City',
        ]);

        // Create a category
        $category = Category::create([
            'name' => 'Open Division',
        ]);

        // Create a tournament
        $tournament = Tournament::create([
            'name' => 'Sports Club Championship 2024',
            'date_start' => now()->addDays(7),
            'date_end' => now()->addDays(8),
            'user_id' => 1, // Use admin user
            'venue_id' => $venue->id,
        ]);

        // Create championship
        $championship = Championship::create([
            'tournament_id' => $tournament->id,
            'category_id' => $category->id,
        ]);

        // Create 8 competitors
        for ($i = 1; $i <= 8; $i++) {
            $user = User::create([
                'name' => 'Player ' . $i,
                'email' => 'player' . $i . '@sportsclub.test',
                'password' => bcrypt('password'),
            ]);

            Competitor::create([
                'championship_id' => $championship->id,
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('Demo tournament created with ID: ' . $tournament->id);
        $this->command->info('Championship created with ID: ' . $championship->id);
        $this->command->info('8 competitors created');
    }
}
