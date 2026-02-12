<?php

namespace App\Models\News;

use App\Models\User;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class NewsComment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'news_post_id',
        'name',
        'email',
        'comment',
        'approved',
        'approved_at',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected $with = ['user'];

    // ========== RELATIONSHIPS ==========

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(NewsPost::class, 'news_post_id');
    }

    // ========== SCOPES ==========

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approved', false);
    }

    // ========== METHODS ==========

    public function approve(): bool
    {
        return $this->update([
            'approved' => true,
            'approved_at' => now(),
        ]);
    }

    public function reject(): bool
    {
        return $this->update([
            'approved' => false,
            'approved_at' => null,
        ]);
    }

    public function isApproved(): bool
    {
        return $this->approved === true;
    }

    public function isPending(): bool
    {
        return $this->approved === false;
    }

    /**
     * Get author name (user name or guest name)
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->name ?? 'Guest');
    }
}