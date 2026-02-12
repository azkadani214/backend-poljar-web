<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('positions')->truncate();
        DB::table('divisions')->truncate();
        Schema::enableForeignKeyConstraints();

        $divisions = [
            [
                'name' => 'Badan Pengurus Harian',
                'positions' => [
                    ['name' => 'Ketua Umum', 'level' => 1],
                    ['name' => 'Wakil Ketua Umum', 'level' => 1],
                    ['name' => 'Sekretaris Jenderal', 'level' => 1],
                    ['name' => 'Bendahara Umum', 'level' => 1],
                ]
            ],
            [
                'name' => 'Divisi Pendidikan & Riset',
                'positions' => [
                    ['name' => 'Koordinator Pendidikan', 'level' => 2],
                    ['name' => 'Sekretaris Divisi Pendidikan', 'level' => 2],
                    ['name' => 'Staff Bidang Kurikulum', 'level' => 3],
                    ['name' => 'Staff Bidang Riset', 'level' => 3],
                ]
            ],
            [
                'name' => 'Divisi Sosial & Pengabdian',
                'positions' => [
                    ['name' => 'Koordinator Sosial', 'level' => 2],
                    ['name' => 'Staff Pemberdayaan Desa', 'level' => 3],
                    ['name' => 'Staff Tanggap Darurat', 'level' => 3],
                ]
            ],
            [
                'name' => 'Divisi Hubungan Masyarakat',
                'positions' => [
                    ['name' => 'Koordinator Humas', 'level' => 2],
                    ['name' => 'Staff Media Cetak', 'level' => 3],
                    ['name' => 'Staff Hubungan Eksternal', 'level' => 3],
                ]
            ],
            [
                'name' => 'Divisi Media & Informasi',
                'positions' => [
                    ['name' => 'Koordinator Media', 'level' => 2],
                    ['name' => 'Staff Graphic Design', 'level' => 3],
                    ['name' => 'Staff Videographer', 'level' => 3],
                    ['name' => 'Staff Content Writer', 'level' => 3],
                ]
            ],
            [
                'name' => 'Divisi Ekonomi Kreatif',
                'positions' => [
                    ['name' => 'Koordinator Ekraf', 'level' => 2],
                    ['name' => 'Staff Kewirausahaan', 'level' => 3],
                    ['name' => 'Staff Partnership', 'level' => 3],
                ]
            ],
        ];

        foreach ($divisions as $divData) {
            $division = Division::create(['name' => $divData['name']]);

            foreach ($divData['positions'] as $posData) {
                Position::create([
                    'division_id' => $division->id,
                    'name' => $posData['name'],
                    'level' => $posData['level'],
                ]);
            }
        }
    }
}
