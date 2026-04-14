<?php

namespace App\Models\Traits;

use App\Helpers\SlugHelper;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $sourceField = !empty($model->title) ? 'title' : (!empty($model->name) ? 'name' : null);
            
            if (empty($model->slug) && $sourceField) {
                $model->slug = SlugHelper::generate(
                    $model->{$sourceField},
                    get_class($model),
                    null,
                    'slug'
                );
            }
        });

        static::updating(function ($model) {
            $sourceField = !empty($model->title) ? 'title' : (!empty($model->name) ? 'name' : null);

            if ($sourceField && $model->isDirty($sourceField) && !$model->isDirty('slug')) {
                $model->slug = SlugHelper::generate(
                    $model->{$sourceField},
                    get_class($model),
                    $model->id,
                    'slug'
                );
            }
        });
    }
}
