<?php

namespace App\Repositories\Eloquent;

use App\Models\News\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NewsletterRepository extends BaseRepository implements NewsletterRepositoryInterface
{
    public function __construct(NewsletterSubscriber $model)
    {
        parent::__construct($model);
    }

    /**
     * Find subscriber by email
     */
    public function findByEmail(string $email): ?NewsletterSubscriber
    {
        return $this->findBy('email', $email);
    }

    /**
     * Get subscribed subscribers
     */
    public function getSubscribed(): Collection
    {
        return $this->model
            ->where('subscribed', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unsubscribed subscribers
     */
    public function getUnsubscribed(): Collection
    {
        return $this->model
            ->where('subscribed', false)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Subscribe email
     */
    public function subscribe(string $email): NewsletterSubscriber
    {
        return $this->updateOrCreate(
            ['email' => $email],
            ['subscribed' => true]
        );
    }

    /**
     * Unsubscribe email
     */
    public function unsubscribe(string $email): bool
    {
        $subscriber = $this->findByEmail($email);
        
        if (!$subscriber) {
            return false;
        }

        return $subscriber->unsubscribe();
    }

    /**
     * Verify subscriber
     */
    public function verify(string $token): bool
    {
        $subscriber = $this->findBy('token', $token);
        
        if (!$subscriber) {
            return false;
        }

        return $subscriber->verify();
    }

    /**
     * Get verified subscribers
     */
    public function getVerified(): Collection
    {
        return $this->model
            ->whereNotNull('verified_at')
            ->where('subscribed', true)
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Get unverified subscribers
     */
    public function getUnverified(): Collection
    {
        return $this->model
            ->whereNull('verified_at')
            ->where('subscribed', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}