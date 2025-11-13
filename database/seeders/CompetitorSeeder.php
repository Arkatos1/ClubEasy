<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Models\Category;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\Tournament;

class CompetitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Competitors seeding!\n";

        $tournament = Tournament::first();
        $individualCategory = Category::where('name', 'Individual')->first();

        if (!$tournament) {
            echo "No tournament found. Please run TournamentSeeder first.\n";
            return;
        }

        // Create Individual Championship
        $individualChampionship = Championship::create([
            'tournament_id' => $tournament->id,
            'category_id' => $individualCategory->id,
        ]);

        // Create some users for competitors (only if we don't have enough)
        $existingUsers = User::count();
        $usersNeeded = 8 - $existingUsers;

        if ($usersNeeded > 0) {
            User::factory($usersNeeded)->create();
        }

        // Get the first 8 users
        $users = User::limit(8)->get();

        // Create competitors
        foreach ($users as $index => $user) {
            Competitor::create([
                'championship_id' => $individualChampionship->id,
                'user_id' => $user->id,
                'confirmed' => 1,
                'short_id' => $index + 1,
            ]);
        }

        echo "Created " . $users->count() . " competitors\n";
    }
}
