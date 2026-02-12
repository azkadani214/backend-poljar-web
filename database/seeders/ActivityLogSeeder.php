<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('activity_logs')->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = User::where('email', 'admin@poljar.com')->first();
        if (!$admin) return;

        $logs = [
            [
                'user_id' => $admin->id,
                'action' => 'login',
                'module' => 'Auth',
                'description' => 'Administrator logged in to the system',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $admin->id,
                'action' => 'create',
                'module' => 'News',
                'description' => 'Created news article: Polinema Mengajar Membuka Rekrutmen...',
                'created_at' => now()->subHour(),
            ],
            [
                'user_id' => $admin->id,
                'action' => 'update',
                'module' => 'User',
                'description' => 'Updated status for user: Budi Santoso',
                'created_at' => now()->subMinutes(30),
            ],
            [
                'user_id' => $admin->id,
                'action' => 'delete',
                'module' => 'Blog',
                'description' => 'Deleted a draft blog post',
                'created_at' => now()->subMinutes(5),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
