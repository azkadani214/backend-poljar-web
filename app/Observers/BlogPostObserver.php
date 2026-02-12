<?php

namespace App\Observers;

use App\Models\Blog\BlogPost;
use App\Helpers\ActivityLogger;
use App\Services\Newsletter\CampaignService;
use App\Models\Newsletter\NewsletterTemplate;
use App\Models\Newsletter\NewsletterTopic;

class BlogPostObserver
{
    public function __construct(
        private CampaignService $campaignService
    ) {}

    /**
     * Handle the BlogPost "created" event.
     */
    public function created(BlogPost $blogPost): void
    {
        ActivityLogger::log(
            'created',
            'Blog',
            "Artikel baru '{$blogPost->title}' telah dibuat.",
            ['id' => $blogPost->id, 'title' => $blogPost->title]
        );

        if ($blogPost->status === 'published') {
            $this->triggerNewsletter($blogPost);
        }
    }

    /**
     * Handle the BlogPost "updated" event.
     */
    public function updated(BlogPost $blogPost): void
    {
        if ($blogPost->wasChanged('status')) {
            $oldStatus = $blogPost->getOriginal('status');
            $newStatus = $blogPost->status;
            
            ActivityLogger::log(
                'updated',
                'Blog',
                "Status artikel '{$blogPost->title}' diubah dari {$oldStatus} menjadi {$newStatus}.",
                [
                    'id' => $blogPost->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            );

            // Trigger newsletter if published
            if ($newStatus === 'published' && $oldStatus !== 'published') {
                $this->triggerNewsletter($blogPost);
            }
        }
    }

    /**
     * Trigger automatic newsletter for new blog post
     */
    private function triggerNewsletter(BlogPost $post): void
    {
        try {
            \Illuminate\Support\Facades\Log::info("Triggering automated newsletter for blog post: {$post->title}");

            // Find "New Post" template or generic one
            $template = NewsletterTemplate::where('name', 'like', '%New Post%')
                ->orWhere('name', 'like', '%Artikel%')
                ->first();

            if (!$template) {
                \Illuminate\Support\Facades\Log::warning("No newsletter template found for blog notification.");
                return;
            }

            // Find "Blog" topic
            $topic = NewsletterTopic::where('slug', 'blog')->first();

            \Illuminate\Support\Facades\Log::info("Using template: " . ($template->name ?? 'None') . " and topic: " . ($topic->name ?? 'None'));

            $campaign = $this->campaignService->createCampaign([
                'subject' => "Artikel Baru: {$post->title}",
                'template_id' => $template->id,
                'topic_id' => $topic?->id,
                'post_id' => $post->id,
                'post_type' => 'blog',
            ]);

            \Illuminate\Support\Facades\Log::info("Newsletter campaign created: {$campaign->id}. Sending now...");

            $this->campaignService->sendNow($campaign->id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to trigger automated newsletter: " . $e->getMessage());
        }
    }

    /**
     * Handle the BlogPost "deleted" event.
     */
    public function deleted(BlogPost $blogPost): void
    {
        ActivityLogger::log(
            'deleted',
            'Blog',
            "Artikel '{$blogPost->title}' telah dihapus."
        );
    }
}
