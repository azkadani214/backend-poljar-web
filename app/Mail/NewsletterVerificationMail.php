<?php

namespace App\Mail;

use App\Models\News\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscriber $subscriber
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->subscriber->locale === 'id' 
            ? 'Konfirmasi Langganan Newsletter Polinema Mengajar' 
            : 'Confirm Your Polinema Mengajar Newsletter Subscription';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.verification',
            with: [
                'verificationUrl' => config('app.frontend_url', 'http://localhost:3000') . '/newsletter/verify?token=' . $this->subscriber->token,
            ],
        );
    }
}
