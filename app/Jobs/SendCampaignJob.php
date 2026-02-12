<?php

namespace App\Jobs;

use App\Models\Newsletter\NewsletterCampaign;
use App\Models\Newsletter\NewsletterCampaignLog;
use App\Models\News\NewsletterSubscriber;
use App\Mail\NewsletterCampaignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public NewsletterCampaign $campaign
    ) {}

    public function handle(): void
    {
        // Update status to sending
        $this->campaign->update(['status' => 'sending']);

        // Get recipients based on topic
        $query = NewsletterSubscriber::verified()->subscribed();
        
        if ($this->campaign->topic_id) {
            $query->whereHas('topics', function ($q) {
                $q->where('newsletter_topics.id', $this->campaign->topic_id);
            });
        }

        $subscribers = $query->get();
        $this->campaign->update(['total_recipients' => $subscribers->count()]);

        foreach ($subscribers as $subscriber) {
            try {
                \Illuminate\Support\Facades\Log::info("Sending campaign {$this->campaign->id} to {$subscriber->email}");
                Mail::to($subscriber->email)->send(new NewsletterCampaignMail($this->campaign, $subscriber));
                
                NewsletterCampaignLog::create([
                    'campaign_id' => $this->campaign->id,
                    'subscriber_id' => $subscriber->id,
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send campaign {$this->campaign->id} to {$subscriber->email}: " . $e->getMessage());
                NewsletterCampaignLog::create([
                    'campaign_id' => $this->campaign->id,
                    'subscriber_id' => $subscriber->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // Check if there were any failures
        $failureCount = NewsletterCampaignLog::where('campaign_id', $this->campaign->id)
            ->where('status', 'failed')
            ->count();
            
        $lastError = null;
        if ($failureCount > 0) {
            $lastError = "Failed to send to {$failureCount} recipients. Check logs for details.";
            // Optionally get the first error message
            $firstError = NewsletterCampaignLog::where('campaign_id', $this->campaign->id)
                ->where('status', 'failed')
                ->whereNotNull('error_message')
                ->first();
            if ($firstError) {
                $lastError .= " First error: " . $firstError->error_message;
            }
        }

        // Update status to sent
        $this->campaign->update([
            'status' => $failureCount > 0 && $failureCount == $subscribers->count() ? 'failed' : 'sent',
            'sent_at' => now(),
            'last_error' => $lastError,
        ]);
    }
}
