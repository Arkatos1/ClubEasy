<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrectAdvancedTournamentSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating advanced tournament with correct settings...');

        // Create tournament
        $tournamentId = DB::table('tournament')->insertGetId([
            'name' => 'Advanced Features Demo',
            'slug' => 'advanced-features-demo',
            'dateIni' => now()->addDays(14)->format('Y-m-d'),
            'dateFin' => now()->addDays(15)->format('Y-m-d'),
            'user_id' => 1,
            'sport' => 1,
            'rule_id' => 1,
            'type' => 1,
            'level_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create championship
        $championshipId = DB::table('championship')->insertGetId([
            'tournament_id' => $tournamentId,
            'category_id' => DB::table('category')->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create championship settings with CORRECT column names
        DB::table('championship_settings')->insert([
            'championship_id' => $championshipId,
            'hasPreliminary' => 1, // Enable preliminary rounds
            'preliminaryGroupSize' => 4, // 4 competitors per group
            'preliminaryWinner' => 2, // CORRECT COLUMN NAME - 2 pass from each group
            'fightingAreas' => 2,
            'treeType' => 1,
            'fightDuration' => '05:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create 16 competitors for preliminary rounds
        for ($i = 1; $i <= 16; $i++) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Group Player ' . $i,
                'email' => 'groupplayer' . $i . '@sportsclub.test',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('competitor')->insert([
                'championship_id' => $championshipId,
                'user_id' => $userId,
                'short_id' => $i,
                'confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✅ Advanced tournament created: $tournamentId");
        $this->command->info("✅ Championship with preliminary settings: $championshipId");
        $this->command->info("✅ 16 competitors for group stages");

        // Generate the tournament structure
        $championship = \Xoco70\LaravelTournaments\Models\Championship::find($championshipId);
        $generation = $championship->chooseGenerationStrategy();
        $generation->run();

        $this->command->info("✅ Tournament brackets generated with preliminary rounds!");
        $this->command->info("Visit: /tournaments/$tournamentId/championships/$championshipId");
    }
}
