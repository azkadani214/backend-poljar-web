<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comprehensive migration for all tables in the Poljar Web Backend
 * Based on the Models: User, Division, Position, Membership,
 * NewsPost, NewsCategory, NewsTag, NewsComment, NewsSeoDetail, NewsletterSubscriber,
 * BlogPost, BlogCategory, BlogTag
 */
return new class extends Migration
{
    public function up(): void
    {
        // =====================================================
        // 1. POSITIONS TABLE
        // =====================================================
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('division_id')->constrained('divisions')->cascadeOnDelete();
                $table->string('name');
                $table->integer('level')->default(1);
                $table->timestamps();
            });
        }

        // =====================================================
        // 2. NEWS CATEGORIES TABLE
        // =====================================================
        if (!Schema::hasTable('news_categories')) {
            Schema::create('news_categories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('color')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // =====================================================
        // 3. NEWS TAGS TABLE
        // =====================================================
        if (!Schema::hasTable('news_tags')) {
            Schema::create('news_tags', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // =====================================================
        // 4. NEWS POSTS TABLE
        // =====================================================
        if (!Schema::hasTable('news_posts')) {
            Schema::create('news_posts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('sub_title')->nullable();
                $table->longText('body');
                $table->text('excerpt')->nullable();
                $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->timestamp('scheduled_for')->nullable();
                $table->string('cover_photo_path')->nullable();
                $table->string('photo_alt_text')->nullable();
                $table->unsignedInteger('views')->default(0);
                $table->string('read_time')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'published_at']);
                $table->index('is_featured');
            });
        }

        // =====================================================
        // 5. NEWS CATEGORY POST PIVOT TABLE
        // =====================================================
        if (!Schema::hasTable('news_category_post')) {
            Schema::create('news_category_post', function (Blueprint $table) {
                $table->foreignUuid('news_post_id')->constrained('news_posts')->cascadeOnDelete();
                $table->foreignUuid('news_category_id')->constrained('news_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['news_post_id', 'news_category_id']);
            });
        }

        // =====================================================
        // 6. NEWS POST TAG PIVOT TABLE
        // =====================================================
        if (!Schema::hasTable('news_post_tag')) {
            Schema::create('news_post_tag', function (Blueprint $table) {
                $table->foreignUuid('news_post_id')->constrained('news_posts')->cascadeOnDelete();
                $table->foreignUuid('news_tag_id')->constrained('news_tags')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['news_post_id', 'news_tag_id']);
            });
        }

        // =====================================================
        // 7. NEWS COMMENTS TABLE
        // =====================================================
        if (!Schema::hasTable('news_comments')) {
            Schema::create('news_comments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignUuid('news_post_id')->constrained('news_posts')->cascadeOnDelete();
                $table->text('comment');
                $table->boolean('approved')->default(false);
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->index(['news_post_id', 'approved']);
            });
        }

        // =====================================================
        // 8. NEWS SEO DETAILS TABLE
        // =====================================================
        if (!Schema::hasTable('news_seo_details')) {
            Schema::create('news_seo_details', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('news_post_id')->constrained('news_posts')->cascadeOnDelete();
                $table->string('meta_title')->nullable();
                $table->json('keywords')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();
            });
        }

        // =====================================================
        // 9. NEWSLETTER SUBSCRIBERS TABLE
        // =====================================================
        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('email')->unique();
                $table->boolean('subscribed')->default(true);
                $table->string('token')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->index(['subscribed', 'verified_at']);
            });
        }

        // =====================================================
        // 10. BLOG CATEGORIES TABLE
        // =====================================================
        if (!Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('color')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // =====================================================
        // 11. BLOG TAGS TABLE
        // =====================================================
        if (!Schema::hasTable('blog_tags')) {
            Schema::create('blog_tags', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // =====================================================
        // 12. BLOG POSTS TABLE
        // =====================================================
        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('sub_title')->nullable();
                $table->longText('body');
                $table->text('excerpt')->nullable();
                $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->timestamp('scheduled_for')->nullable();
                $table->string('cover_photo_path')->nullable();
                $table->string('photo_alt_text')->nullable();
                $table->unsignedInteger('views')->default(0);
                $table->string('read_time')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'published_at']);
                $table->index('is_featured');
            });
        }

        // =====================================================
        // 13. BLOG CATEGORY POST PIVOT TABLE
        // =====================================================
        if (!Schema::hasTable('blog_category_post')) {
            Schema::create('blog_category_post', function (Blueprint $table) {
                $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->foreignUuid('blog_category_id')->constrained('blog_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['blog_post_id', 'blog_category_id']);
            });
        }

        // =====================================================
        // 14. BLOG POST TAG PIVOT TABLE
        // =====================================================
        if (!Schema::hasTable('blog_post_tag')) {
            Schema::create('blog_post_tag', function (Blueprint $table) {
                $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->foreignUuid('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
                $table->timestamps();

                $table->primary(['blog_post_id', 'blog_tag_id']);
            });
        }

        // =====================================================
        // 15. BLOG SEO DETAILS TABLE
        // =====================================================
        if (!Schema::hasTable('blog_seo_details')) {
            Schema::create('blog_seo_details', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
                $table->string('meta_title')->nullable();
                $table->json('keywords')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();
            });
        }

        // =====================================================
        // 16. PASSWORD RESET TOKENS TABLE (if not exists)
        // =====================================================
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // =====================================================
        // 17. PERSONAL ACCESS TOKENS (Sanctum)
        // =====================================================
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->uuidMorphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('blog_seo_details');
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_category_post');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('news_seo_details');
        Schema::dropIfExists('news_comments');
        Schema::dropIfExists('news_post_tag');
        Schema::dropIfExists('news_category_post');
        Schema::dropIfExists('news_posts');
        Schema::dropIfExists('news_tags');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::enableForeignKeyConstraints();
    }
};
