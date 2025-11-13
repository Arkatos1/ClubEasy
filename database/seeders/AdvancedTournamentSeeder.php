<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;

class AdvancedTournamentSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating advanced tournament with package features...');

        // Create a tournament with preliminary rounds
        $tournamentId = DB::table('tournament')->insertGetId([
            'name' => 'Advanced Tournament with Preliminaries',
            'slug' => 'advanced-tournament',
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

        // Create championship settings for preliminary rounds
        ChampionshipSettings::create([
            'championship_id' => $championshipId,
            'hasPreliminary' => 1, // Enable preliminary rounds
            'preliminaryGroupSize' => 4, // 4 competitors per group
            'preliminaryPassing' => 2, // 2 pass from each group
            'fightingAreas' => 2,
            'treeType' => 1,
        ]);

        // Create 16 competitors for preliminary rounds
        for ($i = 1; $i <= 16; $i++) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Adv Player ' . $i,
                'email' => 'advplayer' . $i . '@sportsclub.test',
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

        $this->command->info("✅ Advanced tournament created with ID: $tournamentId");
        $this->command->info("✅ Championship with preliminary rounds: $championshipId");
        $this->command->info("✅ 16 competitors for group stages");

        // Generate the tournament structure
        $championship = Championship::find($championshipId);
        $generation = $championship->chooseGenerationStrategy();
        $generation->run();

        $this->command->info("✅ Tournament brackets generated with preliminary rounds!");
    }
}
