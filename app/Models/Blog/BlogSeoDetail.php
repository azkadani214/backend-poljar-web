<?php

namespace App\Models\Blog;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogSeoDetail extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'blog_seo_details';

    protected $fillable = [
        'blog_post_id',
        'meta_title',
        'keywords',
        'meta_description',
    ];

    protected $casts = [
        'keywords' => 'array',
    ];

    // ========== RELATIONSHIPS ==========

    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    // ========== ACCESSORS & MUTATORS ==========

    public function getMetaTitleAttribute($value): string
    {
        return $value ?: $this->post->title;
    }

    public function getMetaDescriptionAttribute($value): string
    {
        return $value ?: $this->post->excerpt;
    }
}
