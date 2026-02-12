<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\Membership;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('role_user')->truncate();
        DB::table('memberships')->truncate();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        $adminRole = Role::where('name', 'administrator')->first();
        $editorRole = Role::where('name', 'admin')->first();
        $authorRole = Role::where('name', 'penulis')->first();

        // 1. Create Administrator Utama (FULL ACCESS)
        $admin = User::create([
            'name' => 'Administrator Utama',
            'email' => 'admin@poljar.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'phone' => '081234567890',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Polinema No. 1, Malang',
            'bio' => 'System Administrator of Polinema Mengajar.',
        ]);
        $admin->roles()->attach($adminRole->id);

        // 2. Create Editor Berita (LIMITED ACCESS - Content & Users)
        $editor = User::create([
            'name' => 'Editor Berita',
            'email' => 'editor@poljar.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'phone' => '081234567891',
            'gender' => 'female',
            'birth_date' => '1992-05-15',
            'address' => 'Jl. Soekarno Hatta No. 9, Malang',
            'bio' => 'News and content editor.',
        ]);
        $editor->roles()->attach($editorRole->id);

        // 3. Create Penulis Blog (ONLY BLOG)
        $author = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'author@poljar.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'phone' => '081234567892',
            'gender' => 'male',
            'birth_date' => '1995-10-20',
            'address' => 'Jl. Veteran No. 5, Malang',
            'bio' => 'Passionate blogger and writer.',
        ]);
        $author->roles()->attach($authorRole->id);

        // 4. Create dummy users with mixed status
        $dummyUsers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'status' => 'active'],
            ['name' => 'Ani Wijaya', 'email' => 'ani@example.com', 'status' => 'active'],
            ['name' => 'Candra Kirana', 'email' => 'candra@example.com', 'status' => 'active'],
            ['name' => 'Dedi Setiadi', 'email' => 'dedi@example.com', 'status' => 'inactive'],
            ['name' => 'Eka Pratama', 'email' => 'eka@example.com', 'status' => 'active'],
            ['name' => 'Fani Rahmawati', 'email' => 'fani@example.com', 'status' => 'active'],
            ['name' => 'Guntur Prabowo', 'email' => 'guntur@example.com', 'status' => 'active'],
            ['name' => 'Hani Susanti', 'email' => 'hani@example.com', 'status' => 'active'],
        ];

        foreach ($dummyUsers as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'status' => $data['status'],
                'gender' => rand(0, 1) ? 'male' : 'female',
                'phone' => '0857' . rand(10000000, 99999999),
            ]);
            
            // Assign casual writer role to dummy users
            $user->roles()->attach($authorRole->id);
        }

        // Assign memberships to all users
        $users = User::all();
        $divisions = Division::all();
        
        // Ensure divisions exist
        if ($divisions->isEmpty()) {
            return;
        }

        foreach ($users as $index => $user) {
            // Pick a division based on index to ensure spread
            $division = $divisions[$index % $divisions->count()];
            
            // Higher level for main admin
            if ($user->email === 'admin@poljar.com') {
                $position = Position::where('level', 1)->first() ?? Position::where('division_id', $division->id)->first();
            } else if ($user->email === 'editor@poljar.com') {
                $position = Position::where('level', 2)->first() ?? Position::where('division_id', $division->id)->first();
            } else {
                $position = Position::where('division_id', $division->id)->where('level', '>', 2)->first() 
                            ?? Position::where('division_id', $division->id)->first();
            }

            if ($position) {
                Membership::create([
                    'user_id' => $user->id,
                    'division_id' => $division->id,
                    'position_id' => $position->id,
                    'is_active' => true,
                    'period' => '2023/2024',
                    'joined_at' => now()->subMonths(rand(1, 12)),
                ]);
            }
        }
    }
}
