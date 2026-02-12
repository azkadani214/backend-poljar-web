<?php
namespace App\Models\News;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsTag extends Model
{
    use HasFactory, HasUuid, HasSlug;

    protected $fillable = [
        'name',
        'slug',
    ];

    // ========== RELATIONSHIPS ==========

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            NewsPost::class,
            'news_post_tag',
            'news_tag_id',
            'news_post_id'
        )->withTimestamps();
    }

    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');
    }

    // ========== METHODS ==========

    public function getPostsCountAttribute(): int
    {
        return $this->publishedPosts()->count();
    }
}