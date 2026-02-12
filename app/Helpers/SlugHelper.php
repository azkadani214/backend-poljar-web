<?php


namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SlugHelper
{
    /**
     * Generate unique slug
     */
    public static function generate(
        string $title,
        string $modelClass,
        ?string $id = null,
        string $column = 'slug'
    ): string {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (self::slugExists($modelClass, $slug, $id, $column)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    private static function slugExists(
        string $modelClass,
        string $slug,
        ?string $id,
        string $column
    ): bool {
        $query = $modelClass::where($column, $slug);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return $query->exists();
    }

    /**
     * Update slug from title
     */
    public static function updateFromTitle(
        Model $model,
        string $titleColumn = 'title',
        string $slugColumn = 'slug'
    ): string {
        $title = $model->{$titleColumn};
        $slug = self::generate(
            $title,
            get_class($model),
            $model->id,
            $slugColumn
        );

        $model->{$slugColumn} = $slug;

        return $slug;
    }
}