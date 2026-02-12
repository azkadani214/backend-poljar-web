<?php

namespace App\Repositories\Contracts;

use App\Models\News\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Collection;

interface NewsletterRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find subscriber by email
     */
    public function findByEmail(string $email): ?NewsletterSubscriber;

    /**
     * Get subscribed subscribers
     */
    public function getSubscribed(): Collection;

    /**
     * Get unsubscribed subscribers
     */
    public function getUnsubscribed(): Collection;

    /**
     * Subscribe email
     */
    public function subscribe(string $email): NewsletterSubscriber;

    /**
     * Unsubscribe email
     */
    public function unsubscribe(string $email): bool;

    /**
     * Verify subscriber
     */
    public function verify(string $token): bool;

    /**
     * Get verified subscribers
     */
    public function getVerified(): Collection;

    /**
     * Get unverified subscribers
     */
    public function getUnverified(): Collection;
}