<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;

class MemberImporterSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'nama' => 'VENUS RAMADHANI FARICHAH',
                'email' => 'venus@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '083111601503',
                'photo' => 'photos/members/venus.jpg',
                'divisi' => 'Badan Pengurus Harian',
                'posisi' => 'Ketua Umum',
                'level' => 1,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AFRIZAL RAFLI KUSUMA WARDANA',
                'email' => 'afrizal@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '083834079959',
                'photo' => 'photos/members/afrizal.jpg',
                'divisi' => 'Badan Pengurus Harian',
                'posisi' => 'Wakil Ketua Umum',
                'level' => 1,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'LASJA AQUILLA SASONGKO',
                'email' => 'lasja@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '081235042120',
                'photo' => 'photos/members/lasja.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Kepala Divisi Kesekretariatan',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AANISAH NABIILA HANUUN',
                'email' => 'aanisah@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '081217493364',
                'photo' => 'photos/members/aanisah.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Koordinator Sekretaris',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'GALUH VIGA',
                'email' => 'galuh@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085733488385',
                'photo' => 'photos/members/galuh.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Wakil Koordinator Sekretaris',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'FADILLAH FIRDAUSI',
                'email' => 'fadillah@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085604142623',
                'photo' => 'photos/members/fadillah.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Koordinator Fundraising',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AMELIA YUNIA YAHYA',
                'email' => 'amelia@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '08983220853',
                'photo' => 'photos/members/amelia.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Wakil Koordinator Fundraising',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DWI TINA NUR QOMARIYAH',
                'email' => 'dwi@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085232952357',
                'photo' => 'photos/members/dwi.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Anggota Sekretaris',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AURA SINTAVIA ALBAB',
                'email' => 'aura@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085704270317',
                'photo' => 'photos/members/aura.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Anggota Sekretaris',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AZZAHRA AULIA RAHMAN',
                'email' => 'azzahra@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '087756662220',
                'photo' => 'photos/members/azzahra.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Anggota Sekretaris',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'MUHAMMAD YUSAAHILA AKBAR RIZIQ',
                'email' => 'yusaahila@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '083866078225',
                'photo' => 'photos/members/yusaahila.jpg',
                'divisi' => 'Divisi Kesekretariatan',
                'posisi' => 'Anggota Sekretaris',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'ALAN FIDIASMARA PUTRA',
                'email' => 'alan@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085707917975',
                'photo' => 'photos/members/alan.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Kepala Divisi Internal',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'NA\'ILAH SALSABILA MAULIDIYAH',
                'email' => 'nailah@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085885431977',
                'photo' => 'photos/members/nailah.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Koordinator HR',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'NASYWA AUDYARIESTY',
                'email' => 'nasywa@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => null,
                'photo' => 'photos/members/nasywa.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Wakil Koordinator HR',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'BUNGA AULIA SARI',
                'email' => 'bunga@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085156021321',
                'photo' => 'photos/members/bunga.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Koordinator LnD',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'MOHAMMAD ALVIN RAMADHANI',
                'email' => 'alvin@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '0895329553571',
                'photo' => null,
                'divisi' => 'Divisi Internal',
                'posisi' => 'Wakil Koordinator LnD',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'RAIHAN AKBAR PUTRA PRASETYO',
                'email' => 'raihan@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '088226142616',
                'photo' => 'photos/members/raihan.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Anggota Internal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'FAREL ALZAKY ROFII',
                'email' => 'farel@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085258320139',
                'photo' => 'photos/members/farel.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Anggota Internal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'ARYAKAVI RADITYA IMARAN',
                'email' => 'aryakavi@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081358358676',
                'photo' => 'photos/members/aryakavi.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Anggota Internal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'RISKY ADI PRASETYA',
                'email' => 'risky@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085335098465',
                'photo' => 'photos/members/risky.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Anggota Internal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'ROSA AZKA NABILAH',
                'email' => 'rosa@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => null,
                'photo' => 'photos/members/rosa.jpg',
                'divisi' => 'Divisi Internal',
                'posisi' => 'Anggota Internal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'EREDITA CASSANDRA',
                'email' => 'eredita@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '082143429548',
                'photo' => 'photos/members/eredita.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Kepala Divisi Eksternal',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'RIVAN FAHLUL',
                'email' => 'rivan@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085655330165',
                'photo' => 'photos/members/rivan.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Koordinator PR',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'VIERRY ALLAM MUBARAK',
                'email' => 'vierry@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081216392906',
                'photo' => 'photos/members/vierry.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Wakil Koordinator PR',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'NAWAF AZRIL ANNAUFAL',
                'email' => 'nawaf@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => null,
                'photo' => 'photos/members/nawaf.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Koordinator Sponsorship',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DEVY AUDITYARAHMA M',
                'email' => 'devy@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => null,
                'photo' => 'photos/members/devy.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Wakil Koordinator Sponsorship',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AHMAD KEVIN MALIK ZAKARIA',
                'email' => 'kevin@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '089601302560',
                'photo' => 'photos/members/kevin.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Anggota Eksternal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'GISKA AISHANDA CHRYSILLA',
                'email' => 'giska@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => null,
                'photo' => 'photos/members/giska.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Anggota Eksternal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'ZAHARA BAIZILKY',
                'email' => 'zahara@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '082131656822',
                'photo' => 'photos/members/zahara.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Anggota Eksternal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DEWI RIFIA ISNAINI BILBINA',
                'email' => 'dewi@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '081331773244',
                'photo' => 'photos/members/dewi.jpg',
                'divisi' => 'Divisi Eksternal',
                'posisi' => 'Anggota Eksternal',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DHARMAZKA ARSYADANI',
                'email' => 'dharmazka@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085163512809',
                'photo' => 'photos/members/dharmazka.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Kepala Divisi Edukret',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'KHANSA ZHAHIRA',
                'email' => 'khansa@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '087750713532',
                'photo' => 'photos/members/khansa.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Koordinator Teaching',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'GARGARINA NANDA ISWATI',
                'email' => 'gargarina@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '087868858837',
                'photo' => 'photos/members/gargarina.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Wakil Koordinator Teaching',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'RIF\'AN NUR ARFIAN',
                'email' => 'rifan@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085645006592',
                'photo' => 'photos/members/rifan.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Koordinator RnD',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'CICI WULANDARI',
                'email' => 'cici@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => null,
                'photo' => 'photos/members/cici.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Wakil Koordinator RnD',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DINA MEI LESTARI',
                'email' => 'dina@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085711796325',
                'photo' => 'photos/members/dina.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'SAFIKA KAREL AZ ZAHRA',
                'email' => 'safika@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '082231017373',
                'photo' => 'photos/members/safika.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'AURORA ARTIKA OCTAVIA RAMADHANI',
                'email' => 'aurora@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '089516554493',
                'photo' => 'photos/members/aurora.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'M. ATHAR PRANADITYO SUWONO',
                'email' => 'athar@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '082321372090',
                'photo' => 'photos/members/athar.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'NAJMA RAHIMA MUTHMAINNAH',
                'email' => 'najma@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '081212448130',
                'photo' => 'photos/members/najma.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DYANA LADY AKTAFIKA',
                'email' => 'dyana@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085927355670',
                'photo' => 'photos/members/dyana.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'FARID HARDIKA',
                'email' => 'farid@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => null,
                'photo' => 'photos/members/farid.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'MUHAMMAD RIZKY FIRDIANSYAH',
                'email' => 'rizky@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => null,
                'photo' => 'photos/members/rizky.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'MUHAMMAD FATAHILLAH ATHABRANI',
                'email' => 'fatahillah@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081392798181',
                'photo' => 'photos/members/fatahillah.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'MUHAMMAD SHABRAN',
                'email' => 'shabran@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085162632677',
                'photo' => 'photos/members/shabran.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'SATRIO AJI WIBOWO',
                'email' => 'satrio@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081226002909',
                'photo' => 'photos/members/satrio.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'NABIL HANIEF MAFAZI',
                'email' => 'nabil@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '082194688438',
                'photo' => 'photos/members/nabil.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'IMATUZZAHRO',
                'email' => 'imatuzzahro@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085746645542',
                'photo' => 'photos/members/imatuzzahro.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'RINGGA BUDI UTAMA',
                'email' => 'ringga@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '082131648786',
                'photo' => 'photos/members/ringga.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'GANAR SADEWO',
                'email' => 'ganar@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081259625551',
                'photo' => 'photos/members/ganar.jpg',
                'divisi' => 'Divisi Edukasi Kreatif',
                'posisi' => 'Anggota Edukasi Kreatif',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'LEYLILA DEWI AGUSTIN',
                'email' => 'leylila@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '081226794695',
                'photo' => 'photos/members/leylila.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Kepala Divisi Medinfo',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'ZIDANE ALBERT PAUDRALINGGA',
                'email' => 'zidane@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081336537291',
                'photo' => 'photos/members/zidane.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Koordinator Graphic Design',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'BHRE AHMAD YUSUF',
                'email' => 'bhre@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '085179814821',
                'photo' => 'photos/members/bhre.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Wakil Koordinator Graphic Design',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'RAYYAN DIMAS',
                'email' => 'rayyan@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081585216311',
                'photo' => 'photos/members/rayyan.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Koordinator Content Creator',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'SANDRI EMERKRIN OTPAH',
                'email' => 'sandri@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '081246746851',
                'photo' => 'photos/members/sandri.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Koordinator Social Media Specialist',
                'level' => 2,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'SENA ANUGRAH FAKIH',
                'email' => 'sena@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '0896653020949',
                'photo' => 'photos/members/sena.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Anggota Medinfo',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'NOOR ARTIKA SARI',
                'email' => 'noor@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '085731350030',
                'photo' => 'photos/members/noor.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Anggota Medinfo',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'DIVA PERMATA ANDINI',
                'email' => 'diva@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '087841907785',
                'photo' => 'photos/members/diva.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Anggota Medinfo',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'BISMA ADHIAKSA',
                'email' => 'bisma@polinemamengajar.or.id',
                'gender' => 'male',
                'phone' => '081217238501',
                'photo' => 'photos/members/bisma.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Anggota Medinfo',
                'level' => 3,
                'period' => '2024/2025'
            ],
            [
                'nama' => 'TIARA RAHMALIA WIJAYA',
                'email' => 'tiara@polinemamengajar.or.id',
                'gender' => 'female',
                'phone' => '083135081544',
                'photo' => 'photos/members/tiara.jpg',
                'divisi' => 'Divisi Media & Informasi',
                'posisi' => 'Anggota Medinfo',
                'level' => 3,
                'period' => '2024/2025'
            ],
        ];

        DB::beginTransaction();

        try {
            $insertedCount = 0;
            foreach ($members as $data) {
                // 1. Dapatkan atau buat Divisi
                $division = Division::firstOrCreate(
                    ['name' => $data['divisi']]
                );

                // 2. Dapatkan atau buat Posisi
                $position = Position::firstOrCreate(
                    [
                        'division_id' => $division->id,
                        'name' => $data['posisi']
                    ],
                    [
                        'level' => $data['level']
                    ]
                );

                // 3. Buat User (jika email belum terdaftar)
                $user = User::where('email', $data['email'])->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $data['nama'],
                        'email' => $data['email'],
                        'password' => 'poljar123', // password default
                        'phone' => $data['phone'],
                        'photo' => $data['photo'],
                        'gender' => $data['gender'],
                        'status' => 'active'
                    ]);
                } else {
                    // Update photo and phone if user already exists
                    $user->update([
                        'photo' => $data['photo'],
                        'phone' => $data['phone']
                    ]);
                }

                // 4. Buat Keanggotaan jika belum ada relasi yang sama untuk periode ini
                $membershipExists = Membership::where('user_id', $user->id)
                    ->where('division_id', $division->id)
                    ->where('position_id', $position->id)
                    ->where('period', $data['period'])
                    ->exists();

                if (!$membershipExists) {
                    Membership::create([
                        'user_id' => $user->id,
                        'division_id' => $division->id,
                        'position_id' => $position->id,
                        'period' => $data['period'],
                        'is_active' => true,
                        'joined_at' => now()
                    ]);
                    $insertedCount++;
                }
            }

            DB::commit();
            $this->command->info("====== IMPORT SELESAI ======");
            $this->command->info("Berhasil memasukkan/memperbarui {$insertedCount} data keanggotaan baru.");
            $this->command->info("Password default untuk semua akun baru: 'poljar123'");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}
