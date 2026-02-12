<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'administrator', 'label' => 'Administrator'],
            ['name' => 'admin', 'label' => 'Admin'],
            ['name' => 'penulis', 'label' => 'Penulis'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
