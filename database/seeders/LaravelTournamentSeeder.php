<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class LaravelTournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->command->info('Seeding tournament data...');

        $this->call([
            VenueSeeder::class,
            CategorySeeder::class,
            TournamentSeeder::class,
            CompetitorSeeder::class,
        ]);

        $this->command->info('Tournament tables seeded!');
        Model::reguard();
    }
}
