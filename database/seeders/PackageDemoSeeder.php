<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Category;

class PackageDemoSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Setting up package demo data...');

        // Create a category if none exists
        if (!DB::table('category')->exists()) {
            DB::table('category')->insert([
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
        }

        // Create a demo tournament if none exists
        if (!Tournament::exists()) {
            Tournament::create([
                'name' => 'Package Demo Tournament',
                'slug' => 'package-demo',
                'dateIni' => now()->format('Y-m-d'),
                'dateFin' => now()->addDays(1)->format('Y-m-d'),
                'user_id' => 1,
                'sport' => 1,
                'rule_id' => 1,
                'type' => 1,
                'level_id' => 1,
            ]);
        }

        $this->command->info('Package demo setup complete!');
        $this->command->info('Visit /trees to generate tournaments.');
    }
}
