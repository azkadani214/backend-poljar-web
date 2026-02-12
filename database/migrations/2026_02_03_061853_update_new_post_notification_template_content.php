<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('newsletter_templates')
            ->where('name', 'New Post Notification')
            ->update([
                'content' => '<h1>Halo {{name}}!</h1><h2>{{title}}</h2><h3>{{sub_title}}</h3><div>{{excerpt}}</div>{{button}}'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('newsletter_templates')
            ->where('name', 'New Post Notification')
            ->update([
                'content' => '<h1>Halo {{name}}!</h1><p>Ada konten baru untuk Anda.</p><div class="content">{{body}}</div><p><a href="{{unsubscribe_url}}">Unsubscribe</a></p>'
            ]);
    }
};
