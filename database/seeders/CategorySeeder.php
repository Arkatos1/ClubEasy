<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Individual',
            'gender' => 'X',
            'isTeam' => 0,
        ]);

        Category::create([
            'name' => 'Team',
            'gender' => 'X',
            'isTeam' => 1,
        ]);
    }
}
