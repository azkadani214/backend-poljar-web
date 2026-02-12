<?php

namespace App\Models\Blog;

use App\Models\User;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class BlogPost extends Model
{
    use HasFactory, HasUuid, HasSlug, SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'sub_title',
        'body',
        'excerpt',
        'status',
        'published_at',
        'scheduled_for',
        'cover_photo_path',
        'photo_alt_text',
        'views',
        'read_time',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'is_featured' => 'boolean',
        'views' => 'integer',
    ];

    protected $with = ['user'];

    // ========== RELATIONSHIPS ==========

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogCategory::class,
            'blog_category_post',
            'blog_post_id',
            'blog_category_id'
        )->withTimestamps();
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function approvedComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BlogComment::class)->where('approved', true);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogTag::class,
            'blog_post_tag',
            'blog_post_id',
            'blog_tag_id'
        )->withTimestamps();
    }

    public function seoDetail(): HasOne
    {
        return $this->hasOne(BlogSeoDetail::class);
    }

    // ========== SCOPES ==========

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_for', '>', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('categories', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByTag(Builder $query, string $tagSlug): Builder
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('sub_title', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%");
        });
    }

    // ========== ACCESSORS & MUTATORS ==========

    public function getCoverPhotoUrlAttribute(): ?string
    {
        if ($this->cover_photo_path) {
            return asset('storage/' . $this->cover_photo_path);
        }
        return null;
    }

    public function getExcerptAttribute($value): string
    {
        if ($value) {
            return $value;
        }
        return substr(strip_tags($this->body), 0, 200) . '...';
    }

    // ========== METHODS ==========

    public function isPublished(): bool
    {
        return $this->status === 'published' 
            && $this->published_at 
            && $this->published_at->lte(now());
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' 
            && $this->scheduled_for 
            && $this->scheduled_for->gt(now());
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function publish(): bool
    {
        return $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish(): bool
    {
        return $this->update([
            'status' => 'draft',
        ]);
    }

    public function schedule(\DateTime $dateTime): bool
    {
        return $this->update([
            'status' => 'scheduled',
            'scheduled_for' => $dateTime,
        ]);
    }

    public function feature(): bool
    {
        return $this->update(['is_featured' => true]);
    }

    public function unfeature(): bool
    {
        return $this->update(['is_featured' => false]);
    }
}
