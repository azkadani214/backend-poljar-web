<?php

namespace Database\Seeders;

use App\Models\Blog\BlogCategory;
use App\Models\Blog\BlogTag;
use App\Models\Blog\BlogPost;
use App\Models\Blog\BlogSeoDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('blog_seo_details')->truncate();
        DB::table('blog_post_tag')->truncate();
        DB::table('blog_category_post')->truncate();
        DB::table('blog_posts')->truncate();
        DB::table('blog_tags')->truncate();
        DB::table('blog_categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $author = User::whereHas('roles', fn($q) => $q->where('name', 'penulis'))->first() ?: User::first();
        if (!$author) return;

        // 1. Categories
        $categories = [
            ['name' => 'Tips & Trick', 'slug' => 'tips-trick', 'color' => '#8b5cf6'],
            ['name' => 'Cerita Inspiratif', 'slug' => 'cerita-inspiratif', 'color' => '#ec4899'],
            ['name' => 'Wawasan', 'slug' => 'wawasan', 'color' => '#14b8a6'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[] = BlogCategory::create($cat);
        }

        // 2. Tags
        $tags = ['Opini', 'Tutorial', 'Pengalaman', 'Relawan'];
        $tagModels = [];
        foreach ($tags as $tagName) {
            $tagModels[] = BlogTag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName)
            ]);
        }

        // 3. Posts
        $posts = [
            [
                'title' => 'Suka Duka Menjadi Relawan Pengajar',
                'excerpt' => 'Berbagi pengalaman unik saat terjun langsung ke lapangan membantu anak-anak belajar.',
                'body' => 'Banyak orang bertanya, apa yang didapat dari menjadi pengajar sukarela? Jawabannya melampaui sekadar kepuasan batin...',
            ],
            [
                'title' => 'Cara Efektif Mengajar Anak Usia Dini',
                'excerpt' => 'Panduan praktis bagi mahasiswa yang ingin memulai pengabdian di bidang pendidikan.',
                'body' => 'Mengajar anak-anak memerlukan kesabaran ekstra dan teknik yang menyenangkan agar materi mudah diserap...',
            ],
            [
                'title' => 'Mengapa Pemberdayaan Masyarakat Penting?',
                'excerpt' => 'Analisis mendalam mengenai dampak jangka panjang dari program pengabdian.',
                'body' => 'Pemberdayaan bukan hanya soal memberi, tapi soal mendampingi hingga masyarakat mampu berdiri di atas kaki sendiri...',
            ]
        ];

        foreach ($posts as $index => $postData) {
            $post = BlogPost::create([
                'user_id' => $author->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'body' => $postData['body'],
                'status' => 'published',
                'published_at' => now(),
            ]);

            // SEO
            BlogSeoDetail::create([
                'blog_post_id' => $post->id,
                'meta_title' => $post->title,
                'meta_description' => $post->excerpt,
            ]);

            // Relations
            $post->categories()->attach($categoryModels[$index % count($categoryModels)]->id);
            $post->tags()->attach($tagModels[rand(0, count($tagModels)-1)]->id);
        }
    }
}
