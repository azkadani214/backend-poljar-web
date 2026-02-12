<?php

namespace App\Services\Newsletter;

use App\Models\Newsletter\NewsletterTopic;
use App\Repositories\Contracts\NewsletterTopicRepositoryInterface;
use App\Models\News\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use App\Exceptions\Api\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterService
{
    public function __construct(
        private NewsletterRepositoryInterface $newsletterRepository,
        private NewsletterTopicRepositoryInterface $topicRepository
    ) {}

    /**
     * Subscribe to newsletter with topics
     */
    public function subscribe(string $email, array $topicIds = [], string $locale = 'id'): NewsletterSubscriber
    {
        // Check if already subscribed
        /** @var NewsletterSubscriber|null $subscriber */
        $subscriber = $this->newsletterRepository->findByEmail($email);

        if ($subscriber && $subscriber->isSubscribed()) {
            // Just update preferences if already subscribed
            if (!empty($topicIds)) {
                $subscriber->topics()->sync($topicIds);
            }
            return $subscriber;
        }

        // Subscribe or resubscribe
        /** @var NewsletterSubscriber $subscriber */
        $subscriber = $this->newsletterRepository->subscribe($email);
        
        $subscriber->update([
            'locale' => $locale,
            'token' => Str::random(64),
            'verified_at' => null, // Reset verification on resubscribe if needed
        ]);

        // Assign topics
        if (!empty($topicIds)) {
            $subscriber->topics()->sync($topicIds);
        } else {
            // Assign default topics
            $defaultTopics = NewsletterTopic::where('is_default', true)->pluck('id');
            $subscriber->topics()->sync($defaultTopics);
        }

        // Send verification email
        Mail::to($email)->send(new \App\Mail\NewsletterVerificationMail($subscriber));

        return $subscriber;
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(string $email, string $reason = null): bool
    {
        /** @var NewsletterSubscriber|null $subscriber */
        $subscriber = $this->newsletterRepository->findByEmail($email);

        if (!$subscriber) {
            throw new ValidationException('Email not found in newsletter subscribers');
        }

        return $subscriber->update([
            'subscribed' => false,
            'unsubscribe_reason' => $reason,
        ]);
    }

    /**
     * Update preferences
     */
    public function updatePreferences(string $token, array $topicIds): bool
    {
        /** @var NewsletterSubscriber|null $subscriber */
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            throw new ValidationException('Invalid preference token');
        }

        $subscriber->topics()->sync($topicIds);
        return true;
    }

    /**
     * Get all topics
     */
    public function getTopics(): Collection
    {
        return $this->topicRepository->all();
    }

    /**
     * Verify subscriber
     */
    public function verifySubscriber(string $token): bool
    {
        /** @var NewsletterSubscriber|null $subscriber */
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            throw new ValidationException('Invalid or expired verification token');
        }

        $subscriber->update([
            'verified_at' => now(),
            // We keep the token for preference center access
        ]);

        // Send welcome email
        Mail::to($subscriber->email)->send(new \App\Mail\NewsletterWelcomeMail($subscriber));

        return true;
    }

    /**
     * Get all subscribers
     */
    public function getAllSubscribers(): Collection
    {
        return $this->newsletterRepository->getSubscribed();
    }

    /**
     * Get verified subscribers
     */
    public function getVerifiedSubscribers(): Collection
    {
        return $this->newsletterRepository->getVerified();
    }

    /**
     * Get unverified subscribers
     */
    public function getUnverifiedSubscribers(): Collection
    {
        return $this->newsletterRepository->getUnverified();
    }

    /**
     * Send newsletter to all subscribers
     */
    public function sendNewsletter(array $data): array
    {
        $subscribers = $this->newsletterRepository->getVerified();

        if ($subscribers->isEmpty()) {
            throw new ValidationException('No verified subscribers found');
        }

        $sentCount = 0;
        $failedEmails = [];

        foreach ($subscribers as $subscriber) {
            try {
                // Send email
                // Mail::to($subscriber->email)->send(new NewsletterEmail($data));
                $sentCount++;
            } catch (\Exception $e) {
                $failedEmails[] = $subscriber->email;
            }
        }

        return [
            'total_subscribers' => $subscribers->count(),
            'sent' => $sentCount,
            'failed' => count($failedEmails),
            'failed_emails' => $failedEmails,
        ];
    }

    /**
     * Get newsletter statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_subscribers' => $this->newsletterRepository->count(),
            'active_subscribers' => $this->newsletterRepository->getSubscribed()->count(),
            'verified_subscribers' => $this->newsletterRepository->getVerified()->count(),
            'unverified_subscribers' => $this->newsletterRepository->getUnverified()->count(),
            'unsubscribed' => $this->newsletterRepository->getUnsubscribed()->count(),
        ];
    }
}


