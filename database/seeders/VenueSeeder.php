<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Models\Venue;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Venues seeding!\n";

        Venue::create([
            'venue_name' => 'Demo Venue',
            'address' => '123 Sports Street',
            'city' => 'Sports City',
            'state' => 'SC',
            'CP' => '12345',
        ]);
    }
}
