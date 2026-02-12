<?php

namespace App\Models\News;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsSeoDetail extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'news_post_id',
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
        return $this->belongsTo(NewsPost::class, 'news_post_id');
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