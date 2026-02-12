<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Newsletter Topics (Categories)
        if (!Schema::hasTable('newsletter_topics')) {
            Schema::create('newsletter_topics', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('description')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // 2. Newsletter Templates
        if (!Schema::hasTable('newsletter_templates')) {
            Schema::create('newsletter_templates', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->text('content'); // HTML content with merge tags
                $table->json('meta')->nullable(); // For design data
                $table->timestamps();
            });
        }

        // 3. Newsletter Campaigns
        if (!Schema::hasTable('newsletter_campaigns')) {
            Schema::create('newsletter_campaigns', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('subject');
                $table->foreignUuid('template_id')->constrained('newsletter_templates')->onDelete('cascade');
                $table->foreignUuid('topic_id')->nullable()->constrained('newsletter_topics')->onDelete('set null');
                $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->integer('total_recipients')->default(0);
                $table->timestamps();
            });
        }

        // 4. Newsletter Campaign Logs (Analytics)
        if (!Schema::hasTable('newsletter_campaign_logs')) {
            Schema::create('newsletter_campaign_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('campaign_id')->constrained('newsletter_campaigns')->onDelete('cascade');
                $table->foreignUuid('subscriber_id')->constrained('newsletter_subscribers')->onDelete('cascade');
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('clicked_at')->nullable();
                $table->string('status')->default('sent'); // delivered, bounced, etc.
                $table->timestamps();
            });
        }

        // 5. Pivot: Subscriber Preferences
        if (!Schema::hasTable('newsletter_subscriber_topic')) {
            Schema::create('newsletter_subscriber_topic', function (Blueprint $table) {
                $table->foreignUuid('subscriber_id')->constrained('newsletter_subscribers')->onDelete('cascade');
                $table->foreignUuid('topic_id')->constrained('newsletter_topics')->onDelete('cascade');
                $table->primary(['subscriber_id', 'topic_id']);
            });
        }

        // Update newsletter_subscribers to include locale and unsubscribe reason
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            if (!Schema::hasColumn('newsletter_subscribers', 'locale')) {
                $table->string('locale', 5)->default('id')->after('verified_at');
            }
            if (!Schema::hasColumn('newsletter_subscribers', 'unsubscribe_reason')) {
                $table->string('unsubscribe_reason')->nullable()->after('subscribed');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriber_topic');
        Schema::dropIfExists('newsletter_campaign_logs');
        Schema::dropIfExists('newsletter_campaigns');
        Schema::dropIfExists('newsletter_templates');
        Schema::dropIfExists('newsletter_topics');
        
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropColumn(['locale', 'unsubscribe_reason']);
        });
    }
};
