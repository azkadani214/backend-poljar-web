<?php

namespace App\Mail;

use App\Models\News\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscriber $subscriber
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->subscriber->locale === 'id' 
            ? 'Selamat Datang di Newsletter Polinema Mengajar' 
            : 'Welcome to Polinema Mengajar Newsletter';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.welcome',
            with: [
                'preferenceUrl' => config('app.frontend_url', 'http://localhost:3000') . '/newsletter/preferences?token=' . $this->subscriber->token,
            ],
        );
    }
}
