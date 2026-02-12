<?php

namespace App\Models\Blog;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogCategory extends Model
{
    use HasFactory, HasUuid, HasSlug;

    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
    ];

    // ========== RELATIONSHIPS ==========

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogPost::class,
            'blog_category_post',
            'blog_category_id',
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
