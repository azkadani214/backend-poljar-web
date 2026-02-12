<?php

namespace App\Models\Newsletter;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterCampaign extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'subject',
        'template_id',
        'topic_id',
        'post_id',
        'post_type',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'last_error',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NewsletterTemplate::class, 'template_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(NewsletterTopic::class, 'topic_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterCampaignLog::class, 'campaign_id');
    }
}
