<?php

namespace Database\Seeders;

use App\Models\News\NewsletterSubscriber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('newsletter_subscribers')->truncate();
        DB::table('newsletter_topics')->truncate();
        DB::table('newsletter_templates')->truncate();
        DB::table('newsletter_subscriber_topic')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Topics
        $topics = [
            ['name' => 'Blog & Artikel', 'slug' => 'blog', 'description' => 'Notifikasi tentang artikel blog terbaru', 'is_default' => true],
            ['name' => 'Berita & Kegiatan', 'slug' => 'news', 'description' => 'Update berita dan kegiatan polinema mengajar', 'is_default' => true],
        ];

        foreach ($topics as $t) {
            \App\Models\Newsletter\NewsletterTopic::create($t);
        }

        // 2. Templates
        $templates = [
            [
                'name' => 'New Post Notification',
                'content' => '<h1>Halo {{name}}!</h1><h2>{{title}}</h2><h3>{{sub_title}}</h3><div>{{excerpt}}</div>{{button}}',
            ],
            [
                'name' => 'Monthly Newsletter',
                'content' => '<h1>Newsletter Bulanan</h1><p>Berikut rangkuman kegiatan bulan ini.</p><div class="content">{{body}}</div>',
            ],
        ];

        foreach ($templates as $temp) {
            \App\Models\Newsletter\NewsletterTemplate::create($temp);
        }

        // 3. Subscribers
        $subscribers = [
            ['email' => 'user1@example.com', 'subscribed' => true],
            ['email' => 'user2@example.com', 'subscribed' => true],
            ['email' => 'user3@example.com', 'subscribed' => false],
            ['email' => 'user4@example.com', 'subscribed' => true],
            ['email' => 'user5@example.com', 'subscribed' => true],
        ];

        foreach ($subscribers as $data) {
            $sub = NewsletterSubscriber::create([
                'email' => $data['email'],
                'subscribed' => $data['subscribed'],
                'verified_at' => $data['subscribed'] ? now() : null,
                'token' => \Illuminate\Support\Str::random(64),
            ]);

            // Assign default topics
            if ($data['subscribed']) {
                $sub->topics()->sync(\App\Models\Newsletter\NewsletterTopic::pluck('id'));
            }
        }
    }
}
