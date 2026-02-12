<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order is important because of foreign keys
        $this->call([
            RolePermissionSeeder::class,
            DivisionSeeder::class,
            UserSeeder::class,
            NewsSeeder::class,
            BlogSeeder::class,
            NewsletterSeeder::class,
            ActivityLogSeeder::class,
            NewsletterSeeder::class,
        ]);
    }
}
