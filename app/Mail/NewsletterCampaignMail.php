<?php

namespace App\Mail;

use App\Models\Newsletter\NewsletterCampaign;
use App\Models\News\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterCampaign $campaign,
        public NewsletterSubscriber $subscriber
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    public function content(): Content
    {
        $postData = [
            '{{title}}' => '',
            '{{sub_title}}' => '',
            '{{excerpt}}' => '',
            '{{post_url}}' => '',
        ];

        if ($this->campaign->post_id && $this->campaign->post_type) {
            $post = null;
            if ($this->campaign->post_type === 'blog') {
                $post = \App\Models\Blog\BlogPost::find($this->campaign->post_id);
            } elseif ($this->campaign->post_type === 'news') {
                $post = \App\Models\News\NewsPost::find($this->campaign->post_id);
            }

            if ($post) {
                $baseUrl = config('app.frontend_url') ?: config('app.url');
                $postData['{{title}}'] = $post->title;
                $postData['{{sub_title}}'] = $post->sub_title ?: '';
                $postData['{{excerpt}}'] = $post->excerpt;
                $postData['{{post_url}}'] = $baseUrl . '/' . $this->campaign->post_type . '/' . $post->slug;
            }
        }

        // Prepare button
        $buttonHtml = '';
        if ($postData['{{post_url}}']) {
            $buttonHtml = '<div style="text-align: center; margin: 30px 0;">
                <a href="' . $postData['{{post_url}}'] . '" class="button">Baca Selengkapnya</a>
                <p style="font-size: 12px; color: #999; margin-top: 10px;">
                    Jika tombol tidak berfungsi, klik link berikut:<br>
                    <a href="' . $postData['{{post_url}}'] . '" style="color: #FF8C42;">' . $postData['{{post_url}}'] . '</a>
                </p>
            </div>';
        }

        // Simple merge tags replacement
        $body = str_replace(
            ['{{name}}', '{{email}}', '{{unsubscribe_url}}', '{{preference_url}}', '{{title}}', '{{sub_title}}', '{{excerpt}}', '{{post_url}}', '{{button}}'],
            [
                $this->subscriber->name ?: 'Subscriber',
                $this->subscriber->email,
                config('app.url') . '/newsletter/unsubscribe?email=' . $this->subscriber->email,
                config('app.url') . '/newsletter/preferences?token=' . $this->subscriber->token,
                $postData['{{title}}'],
                $postData['{{sub_title}}'],
                $postData['{{excerpt}}'],
                $postData['{{post_url}}'],
                $buttonHtml
            ],
            $this->campaign->template->content
        );

        // Also replace generic {{body}} if it exists in template
        $body = str_replace('{{body}}', '', $body);

        return new Content(
            view: 'emails.newsletter.campaign',
            with: [
                'body' => $body,
                'unsubscribeUrl' => config('app.url') . '/newsletter/unsubscribe?email=' . $this->subscriber->email,
                'preferenceUrl' => config('app.url') . '/newsletter/preferences?token=' . $this->subscriber->token,
            ],
        );
    }
}
