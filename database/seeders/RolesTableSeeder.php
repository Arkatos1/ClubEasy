<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use jeremykenedy\LaravelRoles\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'Visitor',
                'slug' => 'visitor',
                'description' => 'Unauthenticated visitor',
                'level' => 0,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Registered club member',
                'level' => 1,
            ],
            [
                'name' => 'Player',
                'slug' => 'player',
                'description' => 'Sports player',
                'level' => 2,
            ],
            [
                'name' => 'Parent',
                'slug' => 'parent',
                'description' => 'Player parent/guardian',
                'level' => 2,
            ],
            [
                'name' => 'Trainer',
                'slug' => 'trainer',
                'description' => 'Club trainer/coach',
                'level' => 3,
            ],
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'System administrator',
                'level' => 10,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
