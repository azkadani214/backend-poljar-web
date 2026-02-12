<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permission_role')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        $modules = [
            'Berita' => ['view', 'create', 'update', 'delete', 'publish'],
            'Blog' => ['view', 'create', 'update', 'delete', 'publish'],
            'Komentar' => ['view', 'approve', 'delete'],
            'Pengguna' => ['view', 'create', 'update', 'delete'],
            'Organisasi' => ['view', 'create', 'update', 'delete'],
            'Newsletter' => ['view', 'create', 'update', 'delete', 'send'],
            'Statistik' => ['view'],
            'Log Aktivitas' => ['view', 'delete'],
            'Sistem' => ['view', 'update'],
        ];

        $allPermissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                // Determine module key for permission name (handle spaces and special cases)
                $moduleKey = strtolower(str_replace(' ', '', $module));
                if ($moduleKey === 'logaktivitas') $moduleKey = 'log';
                if ($moduleKey === 'statistik') $moduleKey = 'statistik'; // Already matches
                
                $name = $moduleKey . '.' . $action;
                $label = ucfirst($action) . ' ' . $module;
                
                $permission = Permission::updateOrCreate(
                    ['name' => $name],
                    [
                        'label' => $label,
                        'module' => $module
                    ]
                );
                $allPermissions[] = $permission->id;
            }
        }

        // Create Roles
        $adminRole = Role::create([
            'name' => 'administrator',
            'label' => 'Administrator'
        ]);
        // Administrator gets all permissions
        $adminRole->permissions()->sync($allPermissions);

        $editorRole = Role::create([
            'name' => 'admin',
            'label' => 'Admin'
        ]);
        // Admin gets most permissions including Komentar, Newsletter, and Statistik
        $editorPermissions = Permission::whereIn('module', [
            'Berita', 
            'Blog', 
            'Pengguna', 
            'Organisasi', 
            'Komentar', 
            'Newsletter', 
            'Statistik',
            'Log Aktivitas'
        ])->pluck('id');
        $editorRole->permissions()->sync($editorPermissions);

        $penulisRole = Role::create([
            'name' => 'penulis',
            'label' => 'Penulis'
        ]);
        // Penulis gets Blog permissions
        $penulisPermissions = Permission::where('module', 'Blog')->pluck('id');
        $penulisRole->permissions()->sync($penulisPermissions);
    }
}
