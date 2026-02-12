<?php

namespace App\Observers;

use App\Models\News\NewsPost;
use App\Helpers\ActivityLogger;

class NewsPostObserver
{
    public function __construct(
        private \App\Services\Newsletter\CampaignService $campaignService
    ) {}

    /**
     * Handle the NewsPost "created" event.
     */
    public function created(NewsPost $newsPost): void
    {
        ActivityLogger::log(
            'created',
            'News',
            "Berita baru '{$newsPost->title}' telah dibuat.",
            ['id' => $newsPost->id, 'title' => $newsPost->title]
        );

        if ($newsPost->status === 'published') {
            $this->triggerNewsletter($newsPost);
        }
    }

    /**
     * Handle the NewsPost "updated" event.
     */
    public function updated(NewsPost $newsPost): void
    {
        if ($newsPost->wasChanged('status')) {
            $oldStatus = $newsPost->getOriginal('status');
            $newStatus = $newsPost->status;
            
            ActivityLogger::log(
                'updated',
                'News',
                "Status berita '{$newsPost->title}' diubah dari {$oldStatus} menjadi {$newStatus}.",
                [
                    'id' => $newsPost->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            );

            // Trigger newsletter if published
            if ($newStatus === 'published' && $oldStatus !== 'published') {
                $this->triggerNewsletter($newsPost);
            }
        }
    }

    /**
     * Trigger automatic newsletter for new news post
     */
    private function triggerNewsletter(NewsPost $post): void
    {
        try {
            \Illuminate\Support\Facades\Log::info("Triggering automated newsletter for news post: {$post->title}");

            // Find "New Post" template or generic one
            $template = \App\Models\Newsletter\NewsletterTemplate::where('name', 'like', '%New Post%')
                ->orWhere('name', 'like', '%Berita%')
                ->first();

            if (!$template) {
                \Illuminate\Support\Facades\Log::warning("No newsletter template found for news notification.");
                return;
            }

            // Find "News" topic
            $topic = \App\Models\Newsletter\NewsletterTopic::where('slug', 'news')->orWhere('slug', 'berita')->first();

            \Illuminate\Support\Facades\Log::info("Using template: " . ($template->name ?? 'None') . " and topic: " . ($topic->name ?? 'None'));

            $campaign = $this->campaignService->createCampaign([
                'subject' => "Berita Baru: {$post->title}",
                'template_id' => $template->id,
                'topic_id' => $topic?->id,
                'post_id' => $post->id,
                'post_type' => 'news',
            ]);

            \Illuminate\Support\Facades\Log::info("Newsletter campaign created: {$campaign->id}. Sending now...");

            $this->campaignService->sendNow($campaign->id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to trigger automated newsletter for news: " . $e->getMessage());
        }
    }

    /**
     * Handle the NewsPost "deleted" event.
     */
    public function deleted(NewsPost $newsPost): void
    {
        ActivityLogger::log(
            'deleted',
            'News',
            "Berita '{$newsPost->title}' telah dihapus."
        );
    }
}