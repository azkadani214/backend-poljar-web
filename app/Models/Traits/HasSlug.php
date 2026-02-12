<?php

namespace App\Models\Traits;

use App\Helpers\SlugHelper;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = SlugHelper::generate(
                    $model->title,
                    get_class($model),
                    null,
                    'slug'
                );
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && !$model->isDirty('slug')) {
                $model->slug = SlugHelper::generate(
                    $model->title,
                    get_class($model),
                    $model->id,
                    'slug'
                );
            }
        });
    }
}
