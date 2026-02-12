<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // For guest comments
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            
            $table->text('comment');
            $table->boolean('approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['blog_post_id', 'approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
