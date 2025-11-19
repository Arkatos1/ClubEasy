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
                'name' => 'User',
                'slug' => 'registered',
                'description' => 'Basic registered user without membership',
                'level' => 1,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Paid club member with full access',
                'level' => 2,
            ],
            [
                'name' => 'Trainer',
                'slug' => 'trainer',
                'description' => 'Club trainer/coach',
                'level' => 5,
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

        // Delete old roles that are no longer needed
        $oldRoles = ['visitor', 'user', 'player', 'parent'];
        foreach ($oldRoles as $oldRole) {
            Role::where('slug', $oldRole)->delete();
        }
    }
}
