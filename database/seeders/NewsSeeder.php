<?php

namespace Database\Seeders;

use App\Models\News\NewsCategory;
use App\Models\News\NewsTag;
use App\Models\News\NewsPost;
use App\Models\News\NewsComment;
use App\Models\News\NewsSeoDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('news_seo_details')->truncate();
        DB::table('news_post_tag')->truncate();
        DB::table('news_category_post')->truncate();
        DB::table('news_comments')->truncate();
        DB::table('news_posts')->truncate();
        DB::table('news_tags')->truncate();
        DB::table('news_categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = User::first();
        if (!$admin) return;

        // 1. Categories
        $categories = [
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'color' => '#3b82f6'],
            ['name' => 'Sosial', 'slug' => 'sosial', 'color' => '#ef4444'],
            ['name' => 'Event', 'slug' => 'event', 'color' => '#10b981'],
            ['name' => 'Organisasi', 'slug' => 'organisasi', 'color' => '#f59e0b'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[] = NewsCategory::create($cat);
        }

        // 2. Tags
        $tags = ['Polinema', 'Malang', 'Mahasiswa', 'Pemberdayaan', 'Webinar'];
        $tagModels = [];
        foreach ($tags as $tagName) {
            $tagModels[] = NewsTag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName)
            ]);
        }

        // 3. Posts
        $posts = [
            [
                'title' => 'Polinema Mengajar Membuka Rekrutmen Anggota Baru',
                'excerpt' => 'Kesempatan emas bagi mahasiswa Polinema untuk bergabung dalam program pemberdayaan masyarakat.',
                'body' => 'Polinema Mengajar kembali membuka pintu bagi mahasiswa yang bersemangat untuk memberikan kontribusi nyata dalam dunia pendidikan...',
            ],
            [
                'title' => 'Webinar Literasi Digital di Desa Binaan',
                'excerpt' => 'Meningkatkan kesadaran digital warga desa melalui sesi webinar interaktif.',
                'body' => 'Sebagai bagian dari program kerja divisi Media, Polinema Mengajar mengadakan webinar yang dihadiri oleh perangkat desa dan pemuda setempat...',
            ],
            [
                'title' => 'Rapat Kerja Pengurus Polinema Mengajar Periode 2024',
                'excerpt' => 'Menetapkan visi dan misi besar untuk satu tahun ke depan.',
                'body' => 'Seluruh jajaran pengurus baru berkumpul untuk merumuskan agenda besar yang akan dijalankan selama periode kepengurusan ini...',
            ]
        ];

        foreach ($posts as $index => $postData) {
            $post = NewsPost::create([
                'user_id' => $admin->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'body' => $postData['body'],
                'status' => 'published',
                'published_at' => now(),
            ]);

            // SEO
            NewsSeoDetail::create([
                'news_post_id' => $post->id,
                'meta_title' => $post->title,
                'meta_description' => $post->excerpt,
            ]);

            // Relations
            $post->categories()->attach($categoryModels[$index % count($categoryModels)]->id);
            $post->tags()->attach($tagModels[rand(0, count($tagModels)-1)]->id);
        }
    }
}
