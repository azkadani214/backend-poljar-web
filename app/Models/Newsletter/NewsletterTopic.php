<?php

namespace App\Models\Newsletter;

use App\Models\Traits\HasUuid;
use App\Models\News\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterTopic extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            NewsletterSubscriber::class,
            'newsletter_subscriber_topic',
            'topic_id',
            'subscriber_id'
        );
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(NewsletterCampaign::class, 'topic_id');
    }
}
