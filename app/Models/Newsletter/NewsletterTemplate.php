<?php

namespace App\Models\Newsletter;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterTemplate extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'content',
        'meta',
    ];

    protected $casts = [
        'meta' => 'json',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(NewsletterCampaign::class, 'template_id');
    }
}
