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
                'name' => 'Trainer',
                'slug' => 'trainer',
                'description' => 'Club trainer/coach',
                'level' => 2,
            ],
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'System administrator',
                'level' => 3,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
