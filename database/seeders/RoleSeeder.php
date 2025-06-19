<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'admin',
            'coach',
            'player',
            'parent'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
