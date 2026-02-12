<?php

namespace App\Models\Blog;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    use HasFactory, HasUuid, HasSlug;

    protected $table = 'blog_tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    // ========== RELATIONSHIPS ==========

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogPost::class,
            'blog_post_tag',
            'blog_tag_id',
            'blog_post_id'
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
