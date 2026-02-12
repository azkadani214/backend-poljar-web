<?php

namespace App\Models\News;

use App\Models\Traits\HasUuid;
use App\Models\Newsletter\NewsletterTopic;
use App\Models\Newsletter\NewsletterCampaignLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterSubscriber extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'email',
        'name',
        'subscribed',
        'unsubscribe_reason',
        'token',
        'verified_at',
        'locale',
    ];

    protected $casts = [
        'subscribed' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(
            NewsletterTopic::class,
            'newsletter_subscriber_topic',
            'subscriber_id',
            'topic_id'
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterCampaignLog::class, 'subscriber_id');
    }

    // ========== SCOPES ==========

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('subscribed', true);
    }

    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->where('subscribed', false);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereNull('verified_at');
    }

    // ========== METHODS ==========

    public function subscribe(): bool
    {
        return $this->update(['subscribed' => true]);
    }

    public function unsubscribe(): bool
    {
        return $this->update(['subscribed' => false]);
    }

    public function verify(): bool
    {
        return $this->update([
            'verified_at' => now(),
            'token' => null,
        ]);
    }

    public function isSubscribed(): bool
    {
        return $this->subscribed === true;
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function generateVerificationToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['token' => $token]);
        return $token;
    }
}