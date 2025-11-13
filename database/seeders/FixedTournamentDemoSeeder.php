<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixedTournamentDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating fixed demo tournament data...');

        // Create a venue using the correct column names
        $venueId = DB::table('venue')->insertGetId([
            'venue_name' => 'Sports Club Main Arena', // Correct column name
            'address' => '123 Sports Street',
            'city' => 'Sports City',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Created venue with ID: $venueId");

        // Create a category
        $categoryId = DB::table('category')->insertGetId([
            'name' => 'Open Division',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Created category with ID: $categoryId");

        // Create tournament
        $tournamentId = DB::table('tournament')->insertGetId([
            'name' => 'Sports Club Championship 2024',
            'date_start' => now()->addDays(7),
            'date_end' => now()->addDays(8),
            'user_id' => 1, // Use admin user
            'venue_id' => $venueId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Created tournament with ID: $tournamentId");

        // Create championship
        $championshipId = DB::table('championship')->insertGetId([
            'tournament_id' => $tournamentId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Created championship with ID: $championshipId");

        // Create 8 competitors
        for ($i = 1; $i <= 8; $i++) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Tournament Player ' . $i,
                'email' => 'tournament_player' . $i . '@sportsclub.test',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('competitor')->insert([
                'championship_id' => $championshipId,
                'user_id' => $userId,
                'short_id' => 'P' . $i, // Add short_id if required
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("Created competitor $i");
        }

        $this->command->info('Successfully created 8 competitors');

        $this->command->info('Demo tournament creation completed! Tournament ID: ' . $tournamentId);
    }
}
