<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::truncate();

        Event::create([
            'title' => 'Team Training Session 3',
            'description' => 'Regular team practice',
            'start_date' => Carbon::now()->addDays(1)->setHour(18)->setMinute(0),
            'end_date' => Carbon::now()->addDays(1)->setHour(20)->setMinute(0),
        ]);

        Event::create([
            'title' => 'Youth Practice',
            'description' => 'Youth team training',
            'start_date' => Carbon::now()->addDays(3)->setHour(16)->setMinute(0),
            'end_date' => Carbon::now()->addDays(3)->setHour(17)->setMinute(30),
        ]);

        Event::create([
            'title' => 'Weekend Tournament',
            'description' => 'Club tournament',
            'start_date' => Carbon::now()->addDays(6)->setHour(9)->setMinute(0),
            'end_date' => Carbon::now()->addDays(6)->setHour(17)->setMinute(0),
        ]);
    }
}
