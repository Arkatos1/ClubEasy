<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfectTournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating perfect demo tournament...');

        // 1. Create user if needed
        $user = DB::table('users')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Tournament Admin',
                'email' => 'tournament@sportsclub.test',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $user->id;
        }

        // 2. Create venue with ALL required fields
        $venueId = DB::table('venue')->insertGetId([
            'venue_name' => 'Sports Club Arena',
            'city' => 'Sports City',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create category with ALL required fields
        $categoryId = DB::table('category')->insertGetId([
            'name' => 'Open Division',
            'isTeam' => 0,
            'ageCategory' => 0,
            'ageMin' => 0,
            'ageMax' => 0,
            'gradeCategory' => 0,
            'gradeMin' => 0,
            'gradeMax' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Create tournament with ALL required fields
        $tournamentId = DB::table('tournament')->insertGetId([
            'name' => 'Sports Club Championship',
            'slug' => 'sports-club-championship',
            'dateIni' => now()->addDays(7)->format('Y-m-d'), // Must be DATE format
            'dateFin' => now()->addDays(8)->format('Y-m-d'), // Must be DATE format
            'user_id' => $userId,
            'venue_id' => $venueId,
            'sport' => 1,
            'rule_id' => 1,
            'type' => 1,
            'level_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Create championship
        $championshipId = DB::table('championship')->insertGetId([
            'tournament_id' => $tournamentId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Create competitors with ALL required fields (including confirmed)
        for ($i = 1; $i <= 8; $i++) {
            $competitorUserId = DB::table('users')->insertGetId([
                'name' => 'Player ' . $i,
                'email' => 'player' . $i . '@sportsclub.test',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('competitor')->insert([
                'championship_id' => $championshipId,
                'user_id' => $competitorUserId,
                'short_id' => $i,
                'confirmed' => 1, // REQUIRED FIELD - set to 1 (true)
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ PERFECT! Tournament created successfully!');
        $this->command->info("Tournament ID: $tournamentId");
        $this->command->info("Championship ID: $championshipId");
        $this->command->info("8 competitors created with confirmed=1");
    }
}
